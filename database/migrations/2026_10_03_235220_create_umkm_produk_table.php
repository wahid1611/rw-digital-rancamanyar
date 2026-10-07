<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkm_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')->constrained('umkm')->onDelete('cascade');
            $table->string('nama', 150);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2);
            $table->string('satuan', 30)->default('pcs');  // pcs, kg, porsi
            $table->string('foto')->nullable();
            $table->boolean('is_tersedia')->default(true);
            $table->boolean('is_unggulan')->default(false);
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->index(['umkm_id', 'is_tersedia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm_produk');
    }
};