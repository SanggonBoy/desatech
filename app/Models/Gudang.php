<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;

    protected $table = 'gudangs';

    protected $fillable = [
        'kode_gudang',
        'nama_gudang',
        'alamat',
        'pic',
        'no_telp',
        'status'
    ];

    // Relasi ke pembelian
    public function pembelians()
    {
        return $this->hasMany(Pembelian::class);
    }

    // Relasi ke stok gudang
    public function stoks()
    {
        return $this->hasMany(GudangStok::class);
    }

    // Get total item berbeda di gudang
    public function getTotalItemAttribute()
    {
        return $this->stoks()->count();
    }

    // Get total stok semua barang di gudang
    public function getTotalStokAttribute()
    {
        return $this->stoks()->sum('stok');
    }
}
