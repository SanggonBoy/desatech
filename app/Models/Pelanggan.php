<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';

    protected $fillable = [
        'kode_pelanggan',
        'nama_pelanggan',
        'alamat',
        'no_telp',
        'email',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Relasi ke penjualan
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }
}
