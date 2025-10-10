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
        Schema::create('jurnal_umums', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('no_referensi', 50); // No. Pembelian/Penjualan
            $table->string('jenis_transaksi', 20); // pembelian, penjualan, dll
            $table->text('keterangan');
            $table->foreignId('coa_id')->constrained('coas')->onDelete('cascade');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_umums');
    }
};
