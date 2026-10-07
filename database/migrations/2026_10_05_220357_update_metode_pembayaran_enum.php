<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE keuangan_pembayaran MODIFY COLUMN metode ENUM('tunai', 'transfer', 'qris') DEFAULT 'tunai'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE keuangan_pembayaran MODIFY COLUMN metode ENUM('tunai', 'transfer', 'qris', 'ewallet') DEFAULT 'tunai'");
    }
};