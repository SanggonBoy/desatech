<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\GudangStok;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gudangs = Gudang::withCount('stoks')->get();
        return view('gudang.index', compact('gudangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gudang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_gudang' => 'required|unique:gudangs',
            'nama_gudang' => 'required|max:255',
            'alamat' => 'nullable',
            'pic' => 'nullable|max:255',
            'no_telp' => 'nullable|max:20',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Gudang::create($request->all());

        return redirect()->route('gudang.index')
            ->with('success', 'Data gudang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gudang = Gudang::with(['stoks.barang', 'stoks.pembelian'])->findOrFail($id);

        // Group stok by barang untuk tampilan ringkasan
        $stokGrouped = $gudang->stoks()
            ->with('barang')
            ->get()
            ->groupBy('barang_id')
            ->map(function ($batches) {
                $firstBatch = $batches->first();
                return [
                    'barang' => $firstBatch->barang,
                    'total_stok' => $batches->sum('sisa_stok'),
                    'jumlah_batch' => $batches->count(),
                    'batches' => $batches->sortBy('tanggal_masuk'),
                ];
            });

        return view('gudang.show', compact('gudang', 'stokGrouped'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $gudang = Gudang::findOrFail($id);
        return view('gudang.edit', compact('gudang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $gudang = Gudang::findOrFail($id);

        $request->validate([
            'kode_gudang' => 'required|unique:gudangs,kode_gudang,' . $id,
            'nama_gudang' => 'required|max:255',
            'alamat' => 'nullable',
            'pic' => 'nullable|max:255',
            'no_telp' => 'nullable|max:20',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $gudang->update($request->all());

        return redirect()->route('gudang.index')
            ->with('success', 'Data gudang berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $gudang = Gudang::findOrFail($id);

            // Cek apakah masih ada stok di gudang
            $totalStok = $gudang->stoks()->sum('sisa_stok');
            if ($totalStok > 0) {
                return redirect()->back()
                    ->with('error', 'Gudang masih memiliki stok barang, tidak dapat dihapus.');
            }

            $gudang->delete();

            return redirect()->route('gudang.index')
                ->with('success', 'Data gudang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get stock data per gudang untuk API (digunakan di form penjualan)
     */
    public function getStokApi($gudangId)
    {
        try {
            // Ambil semua stok di gudang, group by barang
            $stoks = GudangStok::where('gudang_id', $gudangId)
                ->where('sisa_stok', '>', 0)
                ->with('barang')
                ->get()
                ->groupBy('barang_id')
                ->map(function ($items) {
                    $barang = $items->first()->barang;
                    return [
                        'barang_id' => $barang->id,
                        'kode_barang' => $barang->kode_barang,
                        'nama_barang' => $barang->nama_barang,
                        'satuan' => $barang->satuan_barang,
                        'total_stok' => $items->sum('sisa_stok'),
                    ];
                })->values();

            return response()->json($stoks);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get barang yang tersedia di gudang tertentu (untuk dropdown penjualan)
     */
    public function getBarangByGudang($gudangId)
    {
        try {
            // Ambil barang yang memiliki stok di gudang ini
            $barangs = GudangStok::where('gudang_id', $gudangId)
                ->where('sisa_stok', '>', 0)
                ->with('barang')
                ->get()
                ->groupBy('barang_id')
                ->map(function ($items) {
                    $barang = $items->first()->barang;
                    $totalStok = $items->sum('sisa_stok');
                    return [
                        'id' => $barang->id,
                        'kode_barang' => $barang->kode_barang,
                        'nama_barang' => $barang->nama_barang,
                        'satuan_barang' => $barang->satuan_barang,
                        'harga_jual' => $barang->harga_jual,
                        'total_stok' => $totalStok,
                        // Format untuk display di select2
                        'text' => $barang->kode_barang . ' - ' . $barang->nama_barang . ' (Stok: ' . $totalStok . ' ' . $barang->satuan_barang . ')',
                    ];
                })->values();

            return response()->json($barangs);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
