<?php

namespace App\Services;

use App\Models\GudangStok;
use Illuminate\Support\Facades\DB;

class FifoService
{
    /**
     * Ambil batch yang tersedia untuk barang di gudang tertentu (FIFO order)
     * 
     * @param int $gudangId
     * @param int $barangId
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableBatches($gudangId, $barangId)
    {
        return GudangStok::forItem($gudangId, $barangId)
            ->available()
            ->fifoOrder()
            ->get();
    }

    /**
     * Hitung total stok tersedia untuk barang di gudang
     * 
     * @param int $gudangId
     * @param int $barangId
     * @return int
     */
    public function getTotalStok($gudangId, $barangId)
    {
        return GudangStok::forItem($gudangId, $barangId)
            ->available()
            ->sum('sisa_stok');
    }

    /**
     * Alokasi stok dari batch menggunakan metode FIFO
     * Mengembalikan array dengan detail batch yang digunakan dan HPP total
     * 
     * @param int $gudangId
     * @param int $barangId
     * @param int $jumlahDiminta
     * @return array
     * @throws \Exception
     */
    public function allocateStock($gudangId, $barangId, $jumlahDiminta)
    {
        // Cek ketersediaan total stok
        $totalStok = $this->getTotalStok($gudangId, $barangId);

        if ($totalStok < $jumlahDiminta) {
            throw new \Exception("Stok tidak mencukupi. Stok tersedia: {$totalStok}, diminta: {$jumlahDiminta}");
        }

        $batches = $this->getAvailableBatches($gudangId, $barangId);
        $allocation = [];
        $sisaKebutuhan = $jumlahDiminta;
        $totalHpp = 0;

        foreach ($batches as $batch) {
            if ($sisaKebutuhan <= 0) {
                break;
            }

            // Tentukan berapa banyak yang bisa diambil dari batch ini
            $jumlahDiambil = min($sisaKebutuhan, $batch->sisa_stok);

            // Hitung HPP untuk jumlah yang diambil
            $hppBatch = $jumlahDiambil * $batch->harga_beli_satuan;
            $totalHpp += $hppBatch;

            // Simpan alokasi
            $allocation[] = [
                'batch_id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'jumlah_diambil' => $jumlahDiambil,
                'harga_beli_satuan' => $batch->harga_beli_satuan,
                'hpp_batch' => $hppBatch,
                'sisa_stok_batch_sebelum' => $batch->sisa_stok,
                'sisa_stok_batch_sesudah' => $batch->sisa_stok - $jumlahDiambil,
                'tanggal_masuk' => $batch->tanggal_masuk,
            ];

            $sisaKebutuhan -= $jumlahDiambil;
        }

        return [
            'allocation' => $allocation,
            'total_jumlah' => $jumlahDiminta,
            'total_hpp' => $totalHpp,
            'hpp_rata_rata' => $totalHpp / $jumlahDiminta,
        ];
    }

    /**
     * Eksekusi pengurangan stok dari batch (untuk penjualan)
     * 
     * @param array $allocation - hasil dari allocateStock()
     * @return bool
     */
    public function executeStockReduction($allocation)
    {
        DB::beginTransaction();

        try {
            foreach ($allocation as $item) {
                $batch = GudangStok::findOrFail($item['batch_id']);
                $batch->reduceStock($item['jumlah_diambil']);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Hitung HPP untuk jumlah tertentu tanpa mengeksekusi pengurangan
     * (untuk preview/kalkulasi harga jual)
     * 
     * @param int $gudangId
     * @param int $barangId
     * @param int $jumlah
     * @return array
     */
    public function calculateHPP($gudangId, $barangId, $jumlah)
    {
        try {
            $result = $this->allocateStock($gudangId, $barangId, $jumlah);

            return [
                'success' => true,
                'total_hpp' => $result['total_hpp'],
                'hpp_rata_rata' => $result['hpp_rata_rata'],
                'detail_batch' => $result['allocation'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Ambil informasi detail stok per batch untuk barang di gudang
     * (untuk tampilan/laporan)
     * 
     * @param int $gudangId
     * @param int $barangId
     * @return array
     */
    public function getStockDetail($gudangId, $barangId)
    {
        $batches = $this->getAvailableBatches($gudangId, $barangId);
        $totalStok = $this->getTotalStok($gudangId, $barangId);
        $totalNilai = 0;

        $detail = [];
        foreach ($batches as $batch) {
            $nilaiStok = $batch->sisa_stok * $batch->harga_beli_satuan;
            $totalNilai += $nilaiStok;

            $detail[] = [
                'batch_number' => $batch->batch_number,
                'tanggal_masuk' => $batch->tanggal_masuk->format('d/m/Y'),
                'jumlah_masuk' => $batch->jumlah_masuk,
                'jumlah_keluar' => $batch->jumlah_keluar,
                'sisa_stok' => $batch->sisa_stok,
                'harga_beli_satuan' => number_format($batch->harga_beli_satuan, 0, ',', '.'),
                'nilai_stok' => number_format($nilaiStok, 0, ',', '.'),
                'status' => $batch->status_batch,
            ];
        }

        return [
            'total_stok' => $totalStok,
            'total_nilai' => $totalNilai,
            'hpp_rata_rata' => $totalStok > 0 ? $totalNilai / $totalStok : 0,
            'jumlah_batch' => count($detail),
            'detail_batch' => $detail,
        ];
    }
}
