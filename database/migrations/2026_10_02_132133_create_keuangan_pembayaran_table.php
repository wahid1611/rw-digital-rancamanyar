<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangan_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pembayaran', 30)->unique(); // PAY-20261015-001
            $table->foreignId('tagihan_id')->nullable()->constrained('keuangan_tagihan')->onDelete('set null');
            $table->foreignId('keluarga_id')->constrained('keluarga')->onDelete('cascade');

            $table->decimal('nominal', 12, 2);
            $table->enum('metode', ['tunai', 'transfer', 'qris', 'ewallet'])->default('tunai');
            $table->date('tgl_bayar');
            $table->string('no_referensi', 100)->nullable(); // untuk transfer
            $table->string('bukti')->nullable();             // file bukti transfer
            $table->text('catatan')->nullable();

            // Verifikasi
            $table->enum('status', ['verified', 'pending', 'rejected'])->default('verified');
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->onDelete('set null');

            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['tgl_bayar', 'metode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan_pembayaran');
    }
};