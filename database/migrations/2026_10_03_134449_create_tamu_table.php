<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tamu', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tamu', 30)->unique();  // TMU-20261003-001
            $table->string('qr_code', 50)->unique();    // Kode unik untuk QR

            // Data tamu
            $table->string('nama', 150);
            $table->string('no_hp', 20)->nullable();
            $table->string('no_identitas', 50)->nullable();  // KTP / SIM
            $table->string('jenis_identitas', 20)->nullable(); // KTP / SIM
            $table->string('alamat_asal', 200)->nullable();
            $table->string('instansi', 150)->nullable();  // Perusahaan / komunitas

            // Tujuan
            $table->enum('tujuan_tipe', ['warga', 'rw', 'rt', 'umum', 'lainnya'])->default('warga');
            $table->foreignId('tujuan_user_id')->nullable()->constrained('users')->onDelete('set null')
                  ->comment('User yang dikunjungi');
            $table->foreignId('tujuan_keluarga_id')->nullable()->constrained('keluarga')->onDelete('set null');
            $table->foreignId('tujuan_rt_id')->nullable()->constrained('rt')->onDelete('set null');
            $table->text('keperluan');

            // Waktu
            $table->timestamp('waktu_masuk');
            $table->timestamp('waktu_keluar')->nullable();

            // Foto
            $table->string('foto_tamu')->nullable();
            $table->string('foto_ktp')->nullable();

            // Kendaraan
            $table->string('jenis_kendaraan', 50)->nullable();
            $table->string('plat_nomor', 20)->nullable();

            // Status
            $table->enum('status', ['masuk', 'keluar', 'tidak_kembali'])->default('masuk');

            // Petugas
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index(['status', 'waktu_masuk']);
            $table->index('tujuan_tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tamu');
    }
};