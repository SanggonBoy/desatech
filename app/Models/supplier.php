<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'no_telp',
        'alamat'
    ];

    /**
     * Get WhatsApp formatted phone number
     */
    public function getWhatsappNumberAttribute()
    {
        $cleanNumber = preg_replace('/[^0-9]/', '', $this->no_telp);

        // Jika nomor dimulai dengan 0, ganti dengan 62
        if (substr($cleanNumber, 0, 1) === '0') {
            $cleanNumber = '62' . substr($cleanNumber, 1);
        }

        // Jika nomor tidak dimulai dengan 62, tambahkan 62
        if (substr($cleanNumber, 0, 2) !== '62') {
            $cleanNumber = '62' . $cleanNumber;
        }

        return $cleanNumber;
    }

    /**
     * Get formatted phone number for display
     */
    public function getFormattedPhoneAttribute()
    {
        $phone = $this->no_telp;

        // Format nomor telepon untuk tampilan yang lebih rapi
        if (preg_match('/^(\d{3,4})(\d{3,4})(\d{3,4})/', $phone, $matches)) {
            return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
        }

        return $phone;
    }

    /**
     * Relasi ke pembelian
     */
    public function pembelians()
    {
        return $this->hasMany(Pembelian::class);
    }
}
