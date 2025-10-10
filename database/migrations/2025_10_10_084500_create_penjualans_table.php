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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('no_penjualan')->unique();
            $table->date('tanggal_penjualan');
            $table->foreignId('pelanggan_id')->constrained('pelanggans')->onDelete('restrict');
            $table->foreignId('gudang_id')->constrained('gudangs')->onDelete('restrict');
            $table->decimal('total_penjualan', 15, 2)->default(0); // Total harga jual
            $table->decimal('total_hpp', 15, 2)->default(0); // Total HPP dari FIFO
            $table->decimal('profit', 15, 2)->default(0); // total_penjualan - total_hpp
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'completed'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
