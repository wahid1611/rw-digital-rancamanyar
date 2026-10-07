<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posyandu_jadwal', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);          // "Posyandu Oktober 2026"
            $table->date('tanggal');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('lokasi', 150)->nullable();
            $table->enum('jenis', ['balita', 'lansia', 'umum'])->default('umum');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['rencana', 'berlangsung', 'selesai', 'batal'])->default('rencana');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['tanggal', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posyandu_jadwal');
    }
};