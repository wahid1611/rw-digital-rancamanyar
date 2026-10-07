<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangan_tagihan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tagihan', 30)->unique();   // INV-202610-0001
            $table->foreignId('iuran_id')->constrained('keuangan_iuran')->onDelete('cascade');
            $table->foreignId('keluarga_id')->constrained('keluarga')->onDelete('cascade');
            $table->foreignId('rt_id')->constrained('rt')->onDelete('cascade');

            $table->string('periode', 20);              // "2026-10" atau "2026"
            $table->decimal('nominal', 12, 2);
            $table->decimal('total_dibayar', 12, 2)->default(0);
            $table->decimal('tunggakan', 12, 2)->default(0); // nominal - total_dibayar

            $table->enum('status', ['belum_bayar', 'sebagian', 'lunas', 'telat', 'batal'])
                  ->default('belum_bayar');

            $table->date('jatuh_tempo');
            $table->date('tgl_bayar_lunas')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['iuran_id', 'keluarga_id', 'periode']); // 1 tagihan per iuran+KK+periode
            $table->index(['status', 'jatuh_tempo']);
            $table->index('periode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan_tagihan');
    }
};