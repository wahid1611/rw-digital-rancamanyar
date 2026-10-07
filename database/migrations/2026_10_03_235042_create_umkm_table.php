<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkm', function (Blueprint $table) {
            $table->id();
            $table->string('kode_umkm', 30)->unique();  // UMK-202610-001
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')
                  ->comment('Pemilik UMKM');
            $table->foreignId('rt_id')->nullable()->constrained('rt')->onDelete('set null');

            // Info usaha
            $table->string('nama_usaha', 150);
            $table->enum('kategori', [
                'makanan', 'minuman', 'jasa', 'fashion', 'kerajinan',
                'pertanian', 'elektronik', 'lainnya'
            ])->default('lainnya');
            $table->text('deskripsi')->nullable();

            // Kontak
            $table->string('no_hp', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('instagram', 100)->nullable();

            // Lokasi
            $table->text('alamat')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Foto
            $table->string('logo')->nullable();
            $table->string('foto_usaha')->nullable();

            // Jam operasional
            $table->string('jam_operasional', 100)->nullable();  // "08:00 - 21:00"

            // Status
            $table->enum('status', ['pending', 'aktif', 'nonaktif', 'ditolak'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan_verifikasi')->nullable();

            $table->integer('views')->default(0);
            $table->timestamps();

            $table->index(['status', 'kategori']);
            $table->index('rt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm');
    }
};