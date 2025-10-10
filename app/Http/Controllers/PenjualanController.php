<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Pelanggan;
use App\Models\Gudang;
use App\Models\barang;
use App\Services\FifoService;
use App\Services\JurnalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    protected $fifoService;
    protected $jurnalService;

    public function __construct(FifoService $fifoService, JurnalService $jurnalService)
    {
        $this->fifoService = $fifoService;
        $this->jurnalService = $jurnalService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penjualans = Penjualan::with(['pelanggan', 'gudang'])
            ->orderBy('tanggal_penjualan', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('penjualan.index', compact('penjualans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pelanggans = Pelanggan::where('status', 'aktif')->get();
        $gudangs = Gudang::where('status', 'aktif')->get();
        $barangs = barang::all();
        $noPenjualan = Penjualan::generateNoPenjualan();

        return view('penjualan.create', compact('pelanggans', 'gudangs', 'barangs', 'noPenjualan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_penjualan' => 'required|unique:penjualans,no_penjualan',
            'tanggal_penjualan' => 'required|date',
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'gudang_id' => 'required|exists:gudangs,id',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'required|exists:barang,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $penjualan = Penjualan::create([
                'no_penjualan' => $request->no_penjualan,
                'tanggal_penjualan' => $request->tanggal_penjualan,
                'pelanggan_id' => $request->pelanggan_id,
                'gudang_id' => $request->gudang_id,
                'keterangan' => $request->keterangan,
                'status' => $request->status ?? 'draft',
            ]);

            $totalPenjualan = 0;
            $totalHpp = 0;

            foreach ($request->barang_id as $index => $barangId) {
                $jumlah = $request->jumlah[$index];

                // Hitung HPP dan harga jual dengan profit 30%
                if ($penjualan->status === 'completed') {
                    $stokTersedia = $this->fifoService->getTotalStok($request->gudang_id, $barangId);

                    if ($stokTersedia < $jumlah) {
                        throw new \Exception("Stok barang tidak mencukupi. Tersedia: {$stokTersedia}, Diminta: {$jumlah}");
                    }

                    $allocation = $this->fifoService->allocateStock($request->gudang_id, $barangId, $jumlah);
                    $subtotalHpp = $allocation['total_hpp'];
                    $hppSatuan = $allocation['hpp_rata_rata'];
                    $this->fifoService->executeStockReduction($allocation['allocation']);

                    // Hitung harga jual dengan profit 30%
                    $hargaJualSatuan = $hppSatuan * 1.30; // HPP + 30%
                } else {
                    // Untuk draft, gunakan harga beli dari master barang + 30%
                    $barang = barang::findOrFail($barangId);
                    $hargaBeli = $barang->harga_beli ?? 0;
                    $hargaJualSatuan = $hargaBeli * 1.30; // Harga beli + 30%
                    $subtotalHpp = 0;
                    $hppSatuan = 0;
                }

                $subtotalJual = $jumlah * $hargaJualSatuan;
                $profitItem = $subtotalJual - $subtotalHpp;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $barangId,
                    'jumlah' => $jumlah,
                    'harga_jual_satuan' => $hargaJualSatuan,
                    'subtotal_jual' => $subtotalJual,
                    'hpp_satuan' => $hppSatuan,
                    'subtotal_hpp' => $subtotalHpp,
                    'profit_item' => $profitItem,
                ]);

                $totalPenjualan += $subtotalJual;
                $totalHpp += $subtotalHpp;
            }

            $profit = $totalPenjualan - $totalHpp;
            $penjualan->update([
                'total_penjualan' => $totalPenjualan,
                'total_hpp' => $totalHpp,
                'profit' => $profit,
            ]);

            // Buat jurnal umum jika status completed
            if ($penjualan->status === 'completed') {
                $this->jurnalService->buatJurnalPenjualan($penjualan);
            }

            DB::commit();

            return redirect()->route('penjualan.index')
                ->with('success', 'Data penjualan berhasil disimpan.' .
                    ($penjualan->status === 'completed' ? " Profit: Rp " . number_format($profit, 0, ',', '.') . ". Jurnal umum telah dibuat." : ''));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penjualan = Penjualan::with(['pelanggan', 'gudang', 'details.barang'])->findOrFail($id);
        return view('penjualan.show', compact('penjualan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $penjualan = Penjualan::with('details')->findOrFail($id);

        if ($penjualan->status === 'completed') {
            return redirect()->route('penjualan.index')
                ->with('error', 'Penjualan yang sudah completed tidak dapat diubah.');
        }

        $pelanggans = Pelanggan::where('status', 'aktif')->get();
        $gudangs = Gudang::where('status', 'aktif')->get();
        $barangs = barang::all();

        return view('penjualan.edit', compact('penjualan', 'pelanggans', 'gudangs', 'barangs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        if ($penjualan->status === 'completed') {
            return redirect()->route('penjualan.index')
                ->with('error', 'Penjualan yang sudah completed tidak dapat diupdate.');
        }

        $request->validate([
            'no_penjualan' => 'required|unique:penjualans,no_penjualan,' . $id,
            'tanggal_penjualan' => 'required|date',
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'gudang_id' => 'required|exists:gudangs,id',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'required|exists:barang,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $penjualan->details()->delete();

            $penjualan->update([
                'no_penjualan' => $request->no_penjualan,
                'tanggal_penjualan' => $request->tanggal_penjualan,
                'pelanggan_id' => $request->pelanggan_id,
                'gudang_id' => $request->gudang_id,
                'keterangan' => $request->keterangan,
                'status' => $request->status ?? 'draft',
            ]);

            $totalPenjualan = 0;
            $totalHpp = 0;

            foreach ($request->barang_id as $index => $barangId) {
                $jumlah = $request->jumlah[$index];

                // Hitung HPP dan harga jual dengan profit 30%
                if ($penjualan->status === 'completed') {
                    $stokTersedia = $this->fifoService->getTotalStok($request->gudang_id, $barangId);

                    if ($stokTersedia < $jumlah) {
                        throw new \Exception("Stok barang tidak mencukupi. Tersedia: {$stokTersedia}, Diminta: {$jumlah}");
                    }

                    $allocation = $this->fifoService->allocateStock($request->gudang_id, $barangId, $jumlah);
                    $subtotalHpp = $allocation['total_hpp'];
                    $hppSatuan = $allocation['hpp_rata_rata'];
                    $this->fifoService->executeStockReduction($allocation['allocation']);

                    // Hitung harga jual dengan profit 30%
                    $hargaJualSatuan = $hppSatuan * 1.30; // HPP + 30%
                } else {
                    // Untuk draft, gunakan harga beli dari master barang + 30%
                    $barang = barang::findOrFail($barangId);
                    $hargaBeli = $barang->harga_beli ?? 0;
                    $hargaJualSatuan = $hargaBeli * 1.30; // Harga beli + 30%
                    $subtotalHpp = 0;
                    $hppSatuan = 0;
                }

                $subtotalJual = $jumlah * $hargaJualSatuan;
                $profitItem = $subtotalJual - $subtotalHpp;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $barangId,
                    'jumlah' => $jumlah,
                    'harga_jual_satuan' => $hargaJualSatuan,
                    'subtotal_jual' => $subtotalJual,
                    'hpp_satuan' => $hppSatuan,
                    'subtotal_hpp' => $subtotalHpp,
                    'profit_item' => $profitItem,
                ]);

                $totalPenjualan += $subtotalJual;
                $totalHpp += $subtotalHpp;
            }

            $profit = $totalPenjualan - $totalHpp;
            $penjualan->update([
                'total_penjualan' => $totalPenjualan,
                'total_hpp' => $totalHpp,
                'profit' => $profit,
            ]);

            // Buat jurnal umum jika status completed
            if ($penjualan->status === 'completed') {
                // Hapus jurnal lama jika ada
                $this->jurnalService->hapusJurnalByReferensi($penjualan->no_penjualan);
                // Buat jurnal baru
                $this->jurnalService->buatJurnalPenjualan($penjualan);
            }

            DB::commit();

            return redirect()->route('penjualan.index')
                ->with('success', 'Data penjualan berhasil diupdate.' .
                    ($penjualan->status === 'completed' ? " Profit: Rp " . number_format($profit, 0, ',', '.') . ". Jurnal umum telah dibuat." : ''));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $penjualan = Penjualan::with('details')->findOrFail($id);

            if ($penjualan->status === 'completed') {
                return redirect()->route('penjualan.index')
                    ->with('error', 'Penjualan yang sudah completed tidak dapat dihapus.');
            }

            $penjualan->delete();

            DB::commit();

            return redirect()->route('penjualan.index')
                ->with('success', 'Data penjualan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
