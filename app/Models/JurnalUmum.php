<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'no_referensi',
        'jenis_transaksi',
        'keterangan',
        'coa_id',
        'debit',
        'kredit',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
    ];

    // Relasi ke COA
    public function coa()
    {
        return $this->belongsTo(Coa::class);
    }
}
