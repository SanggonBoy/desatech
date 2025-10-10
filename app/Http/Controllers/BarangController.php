<?php

namespace App\Http\Controllers;

use App\Models\barang;
use App\Http\Requests\StorebarangRequest;
use App\Http\Requests\UpdatebarangRequest;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barang = barang::all();
        return view('barang.index', [
            'barang' => $barang
        ]);
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        // Validasi untuk multiple barang
        $request->validate([
            'barang' => 'required|array|min:1',
            'barang.*.nama_barang' => 'required|string|max:255',
            'barang.*.satuan' => 'required|in:pcs,box,kg,liter,unit',
        ], [
            'barang.required' => 'Minimal harus ada satu barang.',
            'barang.*.nama_barang.required' => 'Nama barang wajib diisi.',
            'barang.*.satuan.required' => 'Satuan barang wajib dipilih.',
            'barang.*.satuan.in' => 'Satuan yang dipilih tidak valid.',
        ]);

        $createdCount = 0;
        $errors = [];

        // Loop untuk setiap barang
        foreach ($request->barang as $index => $barangData) {
            try {
                // Generate kode barang unik
                $lastId = barang::max('id') ?? 0;
                $kode_barang = 'BRG-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

                // Pastikan kode unik
                while (barang::where('kode_barang', $kode_barang)->exists()) {
                    $lastId++;
                    $kode_barang = 'BRG-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
                }

                barang::create([
                    'kode_barang' => $kode_barang,
                    'nama_barang' => $barangData['nama_barang'],
                    'satuan_barang' => $barangData['satuan'],
                ]);

                $createdCount++;
            } catch (\Exception $e) {
                $errors[] = "Gagal menyimpan barang baris " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        // Response berdasarkan hasil
        if ($createdCount > 0 && empty($errors)) {
            $message = $createdCount == 1 ?
                'Barang berhasil ditambahkan.' :
                $createdCount . ' barang berhasil ditambahkan.';
            return redirect('/barang')->with('success', $message);
        } elseif ($createdCount > 0 && !empty($errors)) {
            $message = $createdCount . ' barang berhasil ditambahkan, namun ada beberapa error: ' . implode(', ', $errors);
            return redirect('/barang')->with('warning', $message);
        } else {
            return redirect()->back()->withErrors($errors)->withInput();
        }
    }

    public function edit($id)
    {
        $barang = barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|in:pcs,box,kg,liter,unit',
        ]);

        $barang = barang::findOrFail($id);

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'satuan_barang' => $request->satuan,
        ]);

        return redirect('/barang')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $barang = barang::findOrFail($id);
            $barang->delete();

            return redirect('/barang')->with('success', 'Barang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect('/barang')->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }
}
