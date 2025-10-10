<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JurnalUmum;

class JurnalUmumController extends Controller
{
    public function index(Request $request)
    {
        $query = JurnalUmum::with('coa')->orderBy('tanggal', 'desc')->orderBy('id', 'asc');

        // Filter berdasarkan periode (format: YYYY-MM dari input type month)
        if ($request->filled('periode')) {
            $periode = explode('-', $request->periode); // Split menjadi [tahun, bulan]
            $tahun = $periode[0];
            $bulan = $periode[1];

            $query->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun);
        }

        $jurnals = $query->paginate(50);

        return view('jurnal-umum.index', compact('jurnals'));
    }
}
