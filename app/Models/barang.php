<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan_barang',
        'stok',
        'harga_beli',
        'harga_jual'
    ];

    protected $casts = [
        'stok' => 'integer',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2'
    ];

    /**
     * Relasi ke detail pembelian
     */
    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    /**
     * Relasi ke stok gudang
     */
    public function gudangStoks()
    {
        return $this->hasMany(GudangStok::class);
    }

    /**
     * Get total stok di semua gudang
     */
    public function getTotalStokGudangAttribute()
    {
        return $this->gudangStoks()->sum('stok');
    }
}
