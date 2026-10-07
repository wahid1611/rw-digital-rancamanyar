<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'keluarga_id')) {
                $table->foreignId('keluarga_id')->nullable()->after('rt_id')
                      ->constrained('keluarga')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'keluarga_id')) {
                $table->dropForeign(['keluarga_id']);
                $table->dropColumn('keluarga_id');
            }
        });
    }
};