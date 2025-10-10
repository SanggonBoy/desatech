<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing 'completed' status to 'approved'
        DB::table('pembelians')
            ->where('status', 'completed')
            ->update(['status' => 'approved']);

        // Change the enum to only have draft and approved
        DB::statement("ALTER TABLE pembelians MODIFY COLUMN status ENUM('draft', 'approved') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original enum with three values
        DB::statement("ALTER TABLE pembelians MODIFY COLUMN status ENUM('draft', 'approved', 'completed') DEFAULT 'draft'");
    }
};
