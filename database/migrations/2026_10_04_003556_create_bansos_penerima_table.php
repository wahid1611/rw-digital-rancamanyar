<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bansos_penerima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('bansos_program')->onDelete('cascade');
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade');
            $table->foreignId('keluarga_id')->nullable()->constrained('keluarga')->onDelete('set null');
            $table->foreignId('rt_id')->nullable()->constrained('rt')->onDelete('set null');

            // Data saat didaftarkan
            $table->string('nik_penerima', 16)->nullable();
            $table->string('nama_penerima', 100);
            $table->string('no_hp', 20)->nullable();

            // Kelayakan
            $table->text('alasan_layak')->nullable();
            $table->enum('status_kelayakan', ['pending', 'layak', 'tidak_layak'])
                  ->default('pending');
            $table->integer('skor_kelayakan')->default(0);   // 0-100

            // Verifikasi
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan_verifikasi')->nullable();

            $table->timestamps();

            $table->unique(['program_id', 'warga_id']);       // 1 warga 1x per program
            $table->index('status_kelayakan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bansos_penerima');
    }
};