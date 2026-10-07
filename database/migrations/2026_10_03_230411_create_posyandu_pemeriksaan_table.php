<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posyandu_pemeriksaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('posyandu_jadwal')->onDelete('cascade');
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade');
            $table->foreignId('rt_id')->nullable()->constrained('rt')->onDelete('set null');
            $table->enum('kategori', ['balita', 'lansia', 'ibu_hamil', 'umum'])->default('balita');

            // Data balita
            $table->decimal('berat_badan', 5, 2)->nullable();      // kg
            $table->decimal('tinggi_badan', 5, 2)->nullable();     // cm
            $table->decimal('lingkar_kepala', 5, 2)->nullable();   // cm
            $table->decimal('lingkar_lengan', 5, 2)->nullable();   // cm
            $table->enum('status_gizi', ['normal', 'kurang', 'buruk', 'lebih', 'obesitas'])->nullable();

            // Data lansia
            $table->integer('tekanan_sistolik')->nullable();       // mmHg
            $table->integer('tekanan_diastolik')->nullable();      // mmHg
            $table->integer('gula_darah')->nullable();             // mg/dL
            $table->integer('kolesterol')->nullable();             // mg/dL
            $table->integer('asam_urat')->nullable();              // mg/dL
            $table->decimal('suhu_tubuh', 4, 1)->nullable();       // °C

            // Imunisasi (untuk balita)
            $table->string('imunisasi', 200)->nullable();
            $table->string('vitamin', 100)->nullable();            // Vitamin A, dll

            // Catatan
            $table->text('keluhan')->nullable();
            $table->text('tindakan')->nullable();
            $table->text('catatan')->nullable();

            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['jadwal_id', 'warga_id']);             // 1 warga 1x per jadwal
            $table->index(['kategori', 'status_gizi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posyandu_pemeriksaan');
    }
};