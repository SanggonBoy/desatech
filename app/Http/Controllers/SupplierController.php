<?php

namespace App\Http\Controllers;

use App\Models\supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $supplier = supplier::all();
        return view('supplier.index', [
            'supplier' => $supplier
        ]);
    }

    public function create()
    {
        return view('supplier.create');
    }

    public function store(Request $request)
    {
        // Validasi untuk multiple supplier
        $request->validate([
            'supplier' => 'required|array|min:1',
            'supplier.*.nama_supplier' => 'required|string|max:255',
            'supplier.*.no_telp' => 'required|string|max:20',
            'supplier.*.alamat' => 'required|string|max:500',
        ], [
            'supplier.required' => 'Minimal harus ada satu supplier.',
            'supplier.*.nama_supplier.required' => 'Nama supplier wajib diisi.',
            'supplier.*.no_telp.required' => 'No. telpon wajib diisi.',
            'supplier.*.alamat.required' => 'Alamat wajib diisi.',
        ]);

        $createdCount = 0;
        $errors = [];

        // Loop untuk setiap supplier
        foreach ($request->supplier as $index => $supplierData) {
            try {
                // Generate kode supplier unik
                $lastId = supplier::max('id') ?? 0;
                $kode_supplier = 'SUP-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

                // Pastikan kode unik
                while (supplier::where('kode_supplier', $kode_supplier)->exists()) {
                    $lastId++;
                    $kode_supplier = 'SUP-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
                }

                supplier::create([
                    'kode_supplier' => $kode_supplier,
                    'nama_supplier' => $supplierData['nama_supplier'],
                    'no_telp' => $supplierData['no_telp'],
                    'alamat' => $supplierData['alamat'],
                ]);

                $createdCount++;
            } catch (\Exception $e) {
                $errors[] = "Gagal menyimpan supplier baris " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        // Response berdasarkan hasil
        if ($createdCount > 0 && empty($errors)) {
            $message = $createdCount == 1 ?
                'Supplier berhasil ditambahkan.' :
                $createdCount . ' supplier berhasil ditambahkan.';
            return redirect('/supplier')->with('success', $message);
        } elseif ($createdCount > 0 && !empty($errors)) {
            $message = $createdCount . ' supplier berhasil ditambahkan, namun ada beberapa error: ' . implode(', ', $errors);
            return redirect('/supplier')->with('warning', $message);
        } else {
            return redirect()->back()->withErrors($errors)->withInput();
        }
    }

    public function edit($id)
    {
        $supplier = supplier::findOrFail($id);
        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
        ]);

        $supplier = supplier::findOrFail($id);

        $supplier->update([
            'nama_supplier' => $request->nama_supplier,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
        ]);

        return redirect('/supplier')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $supplier = supplier::findOrFail($id);
            $supplier->delete();

            return redirect('/supplier')->with('success', 'Supplier berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect('/supplier')->with('error', 'Gagal menghapus supplier: ' . $e->getMessage());
        }
    }
}
