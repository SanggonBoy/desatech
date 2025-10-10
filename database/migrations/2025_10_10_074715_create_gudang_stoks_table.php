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
        Schema::create('gudang_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gudang_id')->constrained('gudangs')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->foreignId('pembelian_id')->nullable()->constrained('pembelians')->onDelete('cascade');
            $table->foreignId('pembelian_detail_id')->nullable()->constrained('pembelian_details')->onDelete('cascade');
            $table->string('batch_number'); // Format: BTH-GDG-BRG-YYYYMMDD-XXX
            $table->integer('jumlah_masuk')->default(0); // Qty yang masuk dari pembelian
            $table->integer('jumlah_keluar')->default(0); // Qty yang sudah dijual
            $table->integer('sisa_stok')->default(0); // jumlah_masuk - jumlah_keluar
            $table->decimal('harga_beli_satuan', 15, 2)->default(0); // Harga beli per unit
            $table->date('tanggal_masuk'); // Tanggal batch masuk (dari tgl pembelian)
            $table->date('tanggal_expired')->nullable(); // Optional untuk barang yang ada expired
            $table->timestamps();

            // Index untuk performa FIFO query
            $table->index(['gudang_id', 'barang_id', 'tanggal_masuk']); // Untuk query FIFO
            $table->index('sisa_stok'); // Untuk filter batch yang masih ada stok
            $table->unique('batch_number'); // Batch number harus unique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gudang_stoks');
    }
};
