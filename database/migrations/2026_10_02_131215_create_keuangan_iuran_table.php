<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangan_iuran', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);              // "Iuran Bulanan"
            $table->enum('kategori', [
                'bulanan', 'keamanan', 'kebersihan', 'kesehatan', 'sosial', 'lainnya'
            ])->default('bulanan');
            $table->decimal('nominal_default', 12, 2); // 50000
            $table->enum('periode', ['bulanan', 'triwulan', 'tahunan', 'sekali'])->default('bulanan');
            $table->boolean('per_kk')->default(true);  // true = per KK, false = per orang
            $table->foreignId('rt_id')->nullable()->constrained('rt')->onDelete('cascade')
                  ->comment('Kalau null = berlaku semua RT');

            // Jatuh tempo (tanggal berapa tiap bulan/tahun)
            $table->integer('jatuh_tempo_tgl')->default(10); // tgl 10 setiap bulan
            $table->boolean('is_active')->default(true);

            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'kategori']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan_iuran');
    }
};