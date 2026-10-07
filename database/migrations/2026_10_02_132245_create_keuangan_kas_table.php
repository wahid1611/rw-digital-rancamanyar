<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangan_kas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 30)->unique(); // KAS-20261015-001
            $table->enum('jenis', ['pemasukan', 'pengeluaran']);
            $table->enum('kategori', [
                'iuran', 'sumbangan', 'bantuan', 'denda', // pemasukan
                'operasional', 'perbaikan', 'kegiatan', 'sosial', 'lainnya' // pengeluaran
            ]);
            $table->decimal('nominal', 12, 2);
            $table->date('tgl_transaksi');
            $table->text('deskripsi');
            $table->string('bukti')->nullable();

            // Relasi (kalau dari iuran)
            $table->foreignId('pembayaran_id')->nullable()->constrained('keuangan_pembayaran')->onDelete('set null');

            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['jenis', 'tgl_transaksi']);
            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan_kas');
    }
};