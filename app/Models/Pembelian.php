<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelians';

    protected $fillable = [
        'no_pembelian',
        'tanggal_pembelian',
        'supplier_id',
        'gudang_id',
        'total_pembelian',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'total_pembelian' => 'decimal:2'
    ];

    // Relasi ke supplier
    public function supplier()
    {
        return $this->belongsTo(\App\Models\supplier::class, 'supplier_id');
    }

    // Relasi ke detail pembelian
    public function details()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    // Relasi ke gudang
    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    // Generate nomor pembelian otomatis
    public static function generateNoPembelian()
    {
        $lastPembelian = self::whereYear('tanggal_pembelian', date('Y'))
            ->whereMonth('tanggal_pembelian', date('m'))
            ->orderBy('id', 'desc')
            ->first();

        $lastNumber = $lastPembelian ? (int) substr($lastPembelian->no_pembelian, -4) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return 'PB' . date('Ym') . $newNumber;
    }
}
