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
        Schema::create('penjualan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barang')->onDelete('restrict');
            $table->integer('jumlah');
            $table->decimal('harga_jual_satuan', 15, 2); // Harga jual per unit
            $table->decimal('subtotal_jual', 15, 2); // jumlah × harga_jual_satuan
            $table->decimal('hpp_satuan', 15, 2); // HPP rata-rata dari FIFO allocation
            $table->decimal('subtotal_hpp', 15, 2); // Total HPP untuk item ini
            $table->decimal('profit_item', 15, 2); // subtotal_jual - subtotal_hpp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_details');
    }
};
