<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualans';

    protected $fillable = [
        'no_penjualan',
        'tanggal_penjualan',
        'pelanggan_id',
        'gudang_id',
        'total_penjualan',
        'total_hpp',
        'profit',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'tanggal_penjualan' => 'date',
        'total_penjualan' => 'decimal:2',
        'total_hpp' => 'decimal:2',
        'profit' => 'decimal:2',
        'status' => 'string'
    ];

    // Relasi ke pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    // Relasi ke gudang
    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    // Relasi ke detail penjualan
    public function details()
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    // Accessor: Profit Percentage
    public function getProfitPercentageAttribute()
    {
        if ($this->total_hpp == 0) {
            return 0;
        }
        return ($this->profit / $this->total_hpp) * 100;
    }

    // Accessor: Status Badge Color
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'completed' ? 'success' : 'secondary';
    }

    // Static Method: Generate nomor penjualan
    public static function generateNoPenjualan()
    {
        $date = now()->format('Ym');
        $prefix = "PJ{$date}";

        $lastPenjualan = self::where('no_penjualan', 'LIKE', "{$prefix}%")
            ->orderBy('no_penjualan', 'desc')
            ->first();

        if ($lastPenjualan) {
            $lastNumber = (int) substr($lastPenjualan->no_penjualan, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}{$newNumber}";
    }
}
