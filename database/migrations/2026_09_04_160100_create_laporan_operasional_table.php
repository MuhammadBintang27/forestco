<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_operasional', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('pengguna')->cascadeOnDelete();
            $table->enum('jenis_laporan', ['reservasi'])->default('reservasi');
            $table->enum('format_berkas', ['csv'])->default('csv');
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->timestamp('tanggal_unduh')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_operasional');
    }
};
