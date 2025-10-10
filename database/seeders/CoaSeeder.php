<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coa;

class CoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataCoa = [
            ['kode_akun' => '101', 'nama_akun' => 'Kas'],
            ['kode_akun' => '102', 'nama_akun' => 'Persediaan Barang Dagang'],
            ['kode_akun' => '401', 'nama_akun' => 'Penjualan'],
            ['kode_akun' => '501', 'nama_akun' => 'Beban Gaji'],
            ['kode_akun' => '502', 'nama_akun' => 'Beban Listrik dan Air'],
            ['kode_akun' => '503', 'nama_akun' => 'Beban Sewa Toko'],
            ['kode_akun' => '504', 'nama_akun' => 'Beban Telepon dan Internet'],
            ['kode_akun' => '505', 'nama_akun' => 'Beban Penyusutan'],
            ['kode_akun' => '506', 'nama_akun' => 'Beban Lain-lain'],
            ['kode_akun' => '601', 'nama_akun' => 'Harga Pokok Penjualan'],
        ];

        foreach ($dataCoa as $coa) {
            Coa::create($coa);
        }
    }
}
