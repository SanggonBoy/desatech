<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pelanggans = Pelanggan::withCount('penjualans')->latest()->get();
        return view('pelanggan.index', compact('pelanggans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pelanggan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_pelanggan' => 'required|unique:pelanggans|max:50',
            'nama_pelanggan' => 'required|max:255',
            'alamat' => 'nullable',
            'no_telp' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Pelanggan::create($request->all());

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pelanggan = Pelanggan::with(['penjualans' => function ($query) {
            $query->latest()->limit(10);
        }])->findOrFail($id);

        return view('pelanggan.show', compact('pelanggan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $request->validate([
            'kode_pelanggan' => 'required|max:50|unique:pelanggans,kode_pelanggan,' . $id,
            'nama_pelanggan' => 'required|max:255',
            'alamat' => 'nullable',
            'no_telp' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $pelanggan->update($request->all());

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);

            // Cek apakah pelanggan memiliki transaksi penjualan
            if ($pelanggan->penjualans()->count() > 0) {
                return response()->json([
                    'message' => 'Pelanggan tidak dapat dihapus karena memiliki riwayat transaksi penjualan.'
                ], 400);
            }

            $pelanggan->delete();

            return response()->json([
                'message' => 'Data pelanggan berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
