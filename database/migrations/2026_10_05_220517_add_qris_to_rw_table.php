<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rw', function (Blueprint $table) {
            $table->string('foto_qris')->nullable()->after('alamat_sekretariat');
            $table->string('bank_nama', 100)->nullable()->after('foto_qris');
            $table->string('bank_rekening', 50)->nullable()->after('bank_nama');
            $table->string('bank_atas_nama', 100)->nullable()->after('bank_rekening');
            $table->string('kontak_bendahara', 20)->nullable()->after('bank_atas_nama');
        });
    }

    public function down(): void
    {
        Schema::table('rw', function (Blueprint $table) {
            $table->dropColumn(['foto_qris', 'bank_nama', 'bank_rekening', 'bank_atas_nama', 'kontak_bendahara']);
        });
    }
};