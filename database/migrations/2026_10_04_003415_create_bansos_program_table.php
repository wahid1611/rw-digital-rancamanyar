<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bansos_program', function (Blueprint $table) {
            $table->id();
            $table->string('kode_program', 30)->unique(); // BNS-2026-001
            $table->string('nama', 150);                   // "BLT Desa 2026"
            $table->enum('kategori', [
                'blt', 'pkh', 'bpnt', 'sembako', 'kesehatan',
                'pendidikan', 'bencana', 'lainnya'
            ])->default('lainnya');
            $table->text('deskripsi')->nullable();
            $table->string('sumber', 150)->nullable();     // Pemerintah / Donatur / RW

            // Nilai bantuan
            $table->enum('jenis_bantuan', ['uang', 'barang', 'jasa', 'campuran'])->default('uang');
            $table->decimal('nominal', 15, 2)->nullable();
            $table->string('satuan_bantuan', 50)->nullable(); // bulan, sekali, per keluarga

            // Periode
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->string('periode', 50)->nullable();      // "Bulanan 2026"

            // Kriteria
            $table->text('kriteria')->nullable();           // Siapa yang layak

            // Status
            $table->enum('status', ['draft', 'aktif', 'selesai', 'batal'])->default('draft');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['status', 'kategori']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bansos_program');
    }
};