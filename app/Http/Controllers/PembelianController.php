<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\barang;
use App\Models\supplier;
use App\Models\Gudang;
use App\Models\GudangStok;
use App\Services\JurnalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    protected $jurnalService;

    public function __construct(JurnalService $jurnalService)
    {
        $this->jurnalService = $jurnalService;
    }

    public function index()
    {
        $pembelians = Pembelian::with('supplier')
            ->orderBy('tanggal_pembelian', 'desc')
            ->paginate(10);

        return view('pembelian.index', compact('pembelians'));
    }

    public function create()
    {
        $suppliers = supplier::all();
        $barangs = barang::all();
        $gudangs = Gudang::where('status', 'aktif')->get();
        $noPembelian = Pembelian::generateNoPembelian();

        return view('pembelian.create', compact('suppliers', 'barangs', 'gudangs', 'noPembelian'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_pembelian' => 'required|unique:pembelians',
            'tanggal_pembelian' => 'required|date',
            'supplier_id' => 'required|exists:supplier,id',
            'gudang_id' => 'required|exists:gudangs,id',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'required|exists:barang,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|numeric|min:1',
            'harga_satuan' => 'required|array|min:1',
            'harga_satuan.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalPembelian = 0;
            foreach ($request->barang_id as $index => $barangId) {
                $subtotal = $request->jumlah[$index] * $request->harga_satuan[$index];
                $totalPembelian += $subtotal;
            }

            $pembelian = Pembelian::create([
                'no_pembelian' => $request->no_pembelian,
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'supplier_id' => $request->supplier_id,
                'gudang_id' => $request->gudang_id,
                'total_pembelian' => $totalPembelian,
                'keterangan' => $request->keterangan,
                'status' => $request->status ?? 'draft',
            ]);

            foreach ($request->barang_id as $index => $barangId) {
                $jumlah = $request->jumlah[$index];
                $hargaSatuan = $request->harga_satuan[$index];
                $subtotal = $jumlah * $hargaSatuan;

                $detail = PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $barangId,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ]);

                if ($pembelian->status === 'approved') {
                    $this->updateStokGudang(
                        $request->gudang_id,
                        $barangId,
                        $jumlah,
                        $hargaSatuan,
                        $pembelian->id,
                        $detail->id,
                        $request->tanggal_pembelian
                    );
                }
            }

            if ($pembelian->status === 'approved') {
                $this->jurnalService->buatJurnalPembelian($pembelian);
            }

            DB::commit();

            return redirect()->route('pembelian.index')
                ->with('success', 'Data pembelian berhasil disimpan.' .
                    ($pembelian->status === 'approved' ? ' Jurnal umum telah dibuat.' : ''));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $pembelian = Pembelian::with(['supplier', 'details.barang'])->findOrFail($id);

        return view('pembelian.show', compact('pembelian'));
    }

    public function edit(string $id)
    {
        $pembelian = Pembelian::with('details')->findOrFail($id);

        if ($pembelian->status === 'approved') {
            return redirect()->route('pembelian.index')
                ->with('error', 'Pembelian yang sudah approved tidak dapat diedit.');
        }

        $suppliers = supplier::all();
        $barangs = barang::all();
        $gudangs = Gudang::where('status', 'aktif')->get();

        return view('pembelian.edit', compact('pembelian', 'suppliers', 'barangs', 'gudangs'));
    }

    public function update(Request $request, string $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        if ($pembelian->status === 'approved') {
            return redirect()->route('pembelian.index')
                ->with('error', 'Pembelian yang sudah approved tidak dapat diupdate.');
        }

        $request->validate([
            'no_pembelian' => 'required|unique:pembelians,no_pembelian,' . $id,
            'tanggal_pembelian' => 'required|date',
            'supplier_id' => 'required|exists:supplier,id',
            'gudang_id' => 'required|exists:gudangs,id',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'required|exists:barang,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|numeric|min:1',
            'harga_satuan' => 'required|array|min:1',
            'harga_satuan.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $pembelian->details()->delete();

            $totalPembelian = 0;
            foreach ($request->barang_id as $index => $barangId) {
                $subtotal = $request->jumlah[$index] * $request->harga_satuan[$index];
                $totalPembelian += $subtotal;
            }

            $pembelian->update([
                'no_pembelian' => $request->no_pembelian,
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'supplier_id' => $request->supplier_id,
                'gudang_id' => $request->gudang_id,
                'total_pembelian' => $totalPembelian,
                'keterangan' => $request->keterangan,
                'status' => $request->status ?? 'draft',
            ]);

            foreach ($request->barang_id as $index => $barangId) {
                $jumlah = $request->jumlah[$index];
                $hargaSatuan = $request->harga_satuan[$index];
                $subtotal = $jumlah * $hargaSatuan;

                $detail = PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $barangId,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ]);

                if ($pembelian->status === 'approved') {
                    $this->updateStokGudang(
                        $request->gudang_id,
                        $barangId,
                        $jumlah,
                        $hargaSatuan,
                        $pembelian->id,
                        $detail->id,
                        $request->tanggal_pembelian
                    );
                }
            }

            if ($pembelian->status === 'approved') {
                $this->jurnalService->hapusJurnalByReferensi($pembelian->no_pembelian);
                $this->jurnalService->buatJurnalPembelian($pembelian);
            }

            DB::commit();

            return redirect()->route('pembelian.index')
                ->with('success', 'Data pembelian berhasil diupdate.' .
                    ($pembelian->status === 'approved' ? ' Jurnal umum telah dibuat.' : ''));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $pembelian = Pembelian::with('details')->findOrFail($id);

            if ($pembelian->status === 'approved') {
                return redirect()->route('pembelian.index')
                    ->with('error', 'Pembelian yang sudah approved tidak dapat dihapus.');
            }

            $pembelian->delete();

            DB::commit();

            return redirect()->route('pembelian.index')
                ->with('success', 'Data pembelian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function updateStokGudang($gudangId, $barangId, $jumlah, $hargaSatuan, $pembelianId, $pembelianDetailId, $tanggalPembelian)
    {
        $batchNumber = GudangStok::generateBatchNumber($gudangId, $barangId);

        GudangStok::create([
            'gudang_id' => $gudangId,
            'barang_id' => $barangId,
            'pembelian_id' => $pembelianId,
            'pembelian_detail_id' => $pembelianDetailId,
            'batch_number' => $batchNumber,
            'jumlah_masuk' => $jumlah,
            'jumlah_keluar' => 0,
            'sisa_stok' => $jumlah,
            'harga_beli_satuan' => $hargaSatuan,
            'tanggal_masuk' => $tanggalPembelian,
            'tanggal_expired' => null,
        ]);

        $barang = barang::find($barangId);
        $barang->harga_beli = $hargaSatuan;
        $barang->save();
    }
}
