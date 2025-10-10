<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->integer('stok')->default(0)->after('satuan_barang');
            $table->decimal('harga_beli', 15, 2)->default(0)->after('stok');
            $table->decimal('harga_jual', 15, 2)->default(0)->after('harga_beli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn(['stok', 'harga_beli', 'harga_jual']);
        });
    }
};
