<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Iuran
        Schema::table('keuangan_iuran', function (Blueprint $table) {
            $table->enum('pemilik', ['rw', 'rt'])->default('rw')->after('nama');
            $table->foreignId('pemilik_rt_id')->nullable()->after('pemilik')
                  ->constrained('rt')->onDelete('cascade')
                  ->comment('RT pemilik kalau pemilik=rt');
        });

        // Tagihan
        Schema::table('keuangan_tagihan', function (Blueprint $table) {
            $table->enum('pemilik', ['rw', 'rt'])->default('rw')->after('iuran_id');
        });

        // Kas
        Schema::table('keuangan_kas', function (Blueprint $table) {
            $table->enum('pemilik', ['rw', 'rt'])->default('rw')->after('jenis');
            $table->foreignId('pemilik_rt_id')->nullable()->after('pemilik')
                  ->constrained('rt')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('keuangan_iuran', function (Blueprint $table) {
            $table->dropForeign(['pemilik_rt_id']);
            $table->dropColumn(['pemilik', 'pemilik_rt_id']);
        });
        Schema::table('keuangan_tagihan', function (Blueprint $table) {
            $table->dropColumn('pemilik');
        });
        Schema::table('keuangan_kas', function (Blueprint $table) {
            $table->dropForeign(['pemilik_rt_id']);
            $table->dropColumn(['pemilik', 'pemilik_rt_id']);
        });
    }
};