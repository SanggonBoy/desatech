<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;

    protected $table = 'penjualan_details';

    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'jumlah',
        'harga_jual_satuan',
        'subtotal_jual',
        'hpp_satuan',
        'subtotal_hpp',
        'profit_item'
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_jual_satuan' => 'decimal:2',
        'subtotal_jual' => 'decimal:2',
        'hpp_satuan' => 'decimal:2',
        'subtotal_hpp' => 'decimal:2',
        'profit_item' => 'decimal:2'
    ];

    // Relasi ke penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    // Relasi ke barang
    public function barang()
    {
        return $this->belongsTo(\App\Models\barang::class, 'barang_id');
    }

    // Accessor: Profit Percentage per item
    public function getProfitPercentageAttribute()
    {
        if ($this->subtotal_hpp == 0) {
            return 0;
        }
        return ($this->profit_item / $this->subtotal_hpp) * 100;
    }
}
