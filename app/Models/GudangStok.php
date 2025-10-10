<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GudangStok extends Model
{
    use HasFactory;

    protected $table = 'gudang_stoks';

    protected $fillable = [
        'gudang_id',
        'barang_id',
        'pembelian_id',
        'pembelian_detail_id',
        'batch_number',
        'jumlah_masuk',
        'jumlah_keluar',
        'sisa_stok',
        'harga_beli_satuan',
        'tanggal_masuk',
        'tanggal_expired'
    ];

    protected $casts = [
        'jumlah_masuk' => 'integer',
        'jumlah_keluar' => 'integer',
        'sisa_stok' => 'integer',
        'harga_beli_satuan' => 'decimal:2',
        'tanggal_masuk' => 'date',
        'tanggal_expired' => 'date'
    ];

    // Relasi ke gudang
    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    // Relasi ke barang
    public function barang()
    {
        return $this->belongsTo(\App\Models\barang::class, 'barang_id');
    }

    // Relasi ke pembelian
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    // Relasi ke detail pembelian
    public function pembelianDetail()
    {
        return $this->belongsTo(PembelianDetail::class);
    }

    // Accessor: Hitung total nilai stok batch ini (sisa_stok × harga_beli_satuan)
    public function getTotalNilaiStokAttribute()
    {
        return $this->sisa_stok * $this->harga_beli_satuan;
    }

    // Accessor: Status batch (aktif jika masih ada stok)
    public function getStatusBatchAttribute()
    {
        return $this->sisa_stok > 0 ? 'Aktif' : 'Habis';
    }

    // Accessor: Cek apakah batch sudah expired
    public function getIsExpiredAttribute()
    {
        if (!$this->tanggal_expired) {
            return false;
        }
        return Carbon::now()->greaterThan($this->tanggal_expired);
    }

    // Accessor: Sisa hari sebelum expired
    public function getSisaHariExpiredAttribute()
    {
        if (!$this->tanggal_expired) {
            return null;
        }
        $now = Carbon::now();
        $expired = Carbon::parse($this->tanggal_expired);

        if ($now->greaterThan($expired)) {
            return 0; // Sudah expired
        }

        return $now->diffInDays($expired);
    }

    // Scope: Ambil batch yang masih ada stok
    public function scopeAvailable($query)
    {
        return $query->where('sisa_stok', '>', 0);
    }

    // Scope: FIFO - urutkan dari yang paling lama masuk
    public function scopeFifoOrder($query)
    {
        return $query->orderBy('tanggal_masuk', 'asc')
            ->orderBy('id', 'asc'); // Secondary sort untuk batch yang masuk di hari yang sama
    }

    // Scope: Filter berdasarkan gudang dan barang
    public function scopeForItem($query, $gudangId, $barangId)
    {
        return $query->where('gudang_id', $gudangId)
            ->where('barang_id', $barangId);
    }

    // Method: Kurangi stok batch (untuk penjualan)
    public function reduceStock($jumlah)
    {
        if ($jumlah > $this->sisa_stok) {
            throw new \Exception("Jumlah yang diminta ({$jumlah}) melebihi sisa stok batch ({$this->sisa_stok})");
        }

        $this->jumlah_keluar += $jumlah;
        $this->sisa_stok -= $jumlah;
        $this->save();

        return $this;
    }

    // Static Method: Generate batch number
    public static function generateBatchNumber($gudangId, $barangId)
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "BTH-{$gudangId}-{$barangId}-{$date}";

        // Cari batch terakhir dengan prefix yang sama
        $lastBatch = self::where('batch_number', 'LIKE', "{$prefix}%")
            ->orderBy('batch_number', 'desc')
            ->first();

        if ($lastBatch) {
            // Extract nomor urut terakhir
            $lastNumber = (int) substr($lastBatch->batch_number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "{$prefix}-{$newNumber}";
    }
}
