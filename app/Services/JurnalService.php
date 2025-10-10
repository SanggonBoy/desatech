<?php

namespace App\Services;

use App\Models\JurnalUmum;
use App\Models\Coa;
use Illuminate\Support\Facades\DB;

class JurnalService
{
    /**
     * Buat jurnal untuk transaksi pembelian
     * Debit: Persediaan Barang Dagang (102)
     * Kredit: Kas (101)
     */
    public function buatJurnalPembelian($pembelian)
    {
        DB::beginTransaction();
        try {
            // Ambil COA yang diperlukan
            $coaPersediaan = Coa::where('kode_akun', '102')->first(); // Persediaan Barang Dagang
            $coaKas = Coa::where('kode_akun', '101')->first(); // Kas

            if (!$coaPersediaan || !$coaKas) {
                throw new \Exception('COA tidak ditemukan. Pastikan akun 101 (Kas) dan 102 (Persediaan Barang Dagang) sudah ada.');
            }

            $totalPembelian = $pembelian->total_pembelian;

            // Entry 1: Debit Persediaan Barang Dagang
            JurnalUmum::create([
                'tanggal' => $pembelian->tanggal_pembelian,
                'no_referensi' => $pembelian->no_pembelian,
                'jenis_transaksi' => 'pembelian',
                'keterangan' => 'Pembelian barang dari ' . $pembelian->supplier->nama_supplier,
                'coa_id' => $coaPersediaan->id,
                'debit' => $totalPembelian,
                'kredit' => 0,
            ]);

            // Entry 2: Kredit Kas
            JurnalUmum::create([
                'tanggal' => $pembelian->tanggal_pembelian,
                'no_referensi' => $pembelian->no_pembelian,
                'jenis_transaksi' => 'pembelian',
                'keterangan' => 'Pembelian barang dari ' . $pembelian->supplier->nama_supplier,
                'coa_id' => $coaKas->id,
                'debit' => 0,
                'kredit' => $totalPembelian,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Buat jurnal untuk transaksi penjualan
     * Entry 1: Debit Kas (101)
     * Entry 2: Kredit Penjualan (401)
     * Entry 3: Debit Harga Pokok Penjualan (601)
     * Entry 4: Kredit Persediaan Barang Dagang (102)
     */
    public function buatJurnalPenjualan($penjualan)
    {
        DB::beginTransaction();
        try {
            // Ambil COA yang diperlukan
            $coaKas = Coa::where('kode_akun', '101')->first(); // Kas
            $coaPenjualan = Coa::where('kode_akun', '401')->first(); // Penjualan
            $coaHPP = Coa::where('kode_akun', '601')->first(); // Harga Pokok Penjualan
            $coaPersediaan = Coa::where('kode_akun', '102')->first(); // Persediaan Barang Dagang

            if (!$coaKas || !$coaPenjualan || !$coaHPP || !$coaPersediaan) {
                throw new \Exception('COA tidak ditemukan. Pastikan akun 101 (Kas), 401 (Penjualan), 601 (HPP), dan 102 (Persediaan Barang Dagang) sudah ada.');
            }

            $totalPenjualan = $penjualan->total_penjualan;
            $totalHPP = $penjualan->total_hpp;

            // Entry 1: Debit Kas
            JurnalUmum::create([
                'tanggal' => $penjualan->tanggal_penjualan,
                'no_referensi' => $penjualan->no_penjualan,
                'jenis_transaksi' => 'penjualan',
                'keterangan' => 'Penjualan barang kepada ' . $penjualan->pelanggan->nama_pelanggan,
                'coa_id' => $coaKas->id,
                'debit' => $totalPenjualan,
                'kredit' => 0,
            ]);

            // Entry 2: Kredit Penjualan
            JurnalUmum::create([
                'tanggal' => $penjualan->tanggal_penjualan,
                'no_referensi' => $penjualan->no_penjualan,
                'jenis_transaksi' => 'penjualan',
                'keterangan' => 'Penjualan barang kepada ' . $penjualan->pelanggan->nama_pelanggan,
                'coa_id' => $coaPenjualan->id,
                'debit' => 0,
                'kredit' => $totalPenjualan,
            ]);

            // Entry 3: Debit Harga Pokok Penjualan
            JurnalUmum::create([
                'tanggal' => $penjualan->tanggal_penjualan,
                'no_referensi' => $penjualan->no_penjualan,
                'jenis_transaksi' => 'penjualan',
                'keterangan' => 'Penjualan barang kepada ' . $penjualan->pelanggan->nama_pelanggan,
                'coa_id' => $coaHPP->id,
                'debit' => $totalHPP,
                'kredit' => 0,
            ]);

            // Entry 4: Kredit Persediaan Barang Dagang
            JurnalUmum::create([
                'tanggal' => $penjualan->tanggal_penjualan,
                'no_referensi' => $penjualan->no_penjualan,
                'jenis_transaksi' => 'penjualan',
                'keterangan' => 'Penjualan barang kepada ' . $penjualan->pelanggan->nama_pelanggan,
                'coa_id' => $coaPersediaan->id,
                'debit' => 0,
                'kredit' => $totalHPP,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Hapus jurnal berdasarkan no referensi
     */
    public function hapusJurnalByReferensi($noReferensi)
    {
        JurnalUmum::where('no_referensi', $noReferensi)->delete();
    }

    /**
     * Cek apakah jurnal sudah dibuat untuk referensi tertentu
     */
    public function isJurnalExists($noReferensi)
    {
        return JurnalUmum::where('no_referensi', $noReferensi)->exists();
    }
}
