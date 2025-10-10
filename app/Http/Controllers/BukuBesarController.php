<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coa;
use App\Models\JurnalUmum;
use Illuminate\Support\Facades\DB;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua COA
        $coas = Coa::orderBy('kode_akun', 'asc')->get();

        // Filter berdasarkan periode jika ada
        $periode = $request->periode;
        $bukuBesar = [];

        foreach ($coas as $coa) {
            $query = JurnalUmum::where('coa_id', $coa->id)
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc');

            // Filter berdasarkan periode (format: YYYY-MM dari input type month)
            if ($request->filled('periode')) {
                $periodeArray = explode('-', $request->periode);
                $tahun = $periodeArray[0];
                $bulan = $periodeArray[1];

                $query->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun);
            }

            $transaksis = $query->get();

            // Hitung saldo
            $saldoAwal = 0; // Untuk saat ini saldo awal = 0
            $totalDebit = $transaksis->sum('debit');
            $totalKredit = $transaksis->sum('kredit');
            $saldoAkhir = $saldoAwal + $totalDebit - $totalKredit;

            $bukuBesar[] = [
                'coa' => $coa,
                'transaksis' => $transaksis,
                'saldo_awal' => $saldoAwal,
                'total_debit' => $totalDebit,
                'total_kredit' => $totalKredit,
                'saldo_akhir' => $saldoAkhir,
            ];
        }

        return view('buku-besar.index', compact('bukuBesar', 'periode'));
    }
}
