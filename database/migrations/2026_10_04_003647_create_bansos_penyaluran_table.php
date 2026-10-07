<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bansos_penyaluran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_penyaluran', 30)->unique();  // SLY-20261004-001
            $table->foreignId('penerima_id')->constrained('bansos_penerima')->onDelete('cascade');
            $table->foreignId('program_id')->constrained('bansos_program')->onDelete('cascade');

            // Penyaluran
            $table->date('tanggal');
            $table->string('periode', 50)->nullable();       // "Oktober 2026"
            $table->decimal('nominal', 15, 2)->nullable();    // jumlah yang disalurkan
            $table->string('jenis_bantuan', 50)->nullable();  // uang / barang / jasa
            $table->text('deskripsi_barang')->nullable();     // kalau barang

            // Bukti
            $table->string('foto_bukti')->nullable();
            $table->string('tanda_tangan')->nullable();

            // Status
            $table->enum('status', ['dijadwalkan', 'disalurkan', 'dibatalkan'])->default('disalurkan');
            $table->text('catatan')->nullable();

            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['tanggal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bansos_penyaluran');
    }
};