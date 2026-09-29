<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_kerusakan', function (Blueprint $table) {
            // Bukti foto dari admin begitu laporan ditandai selesai, supaya
            // penyewa bisa lihat sendiri hasil perbaikannya.
            $table->string('foto_selesai_path')->nullable()->after('foto_path');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_kerusakan', function (Blueprint $table) {
            $table->dropColumn('foto_selesai_path');
        });
    }
};
