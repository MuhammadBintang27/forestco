<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kerusakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sewa_id')->constrained('sewa')->cascadeOnDelete();
            // Sesuai ERD: LAPORAN_KERUSAKAN menyimpan id_penyewa langsung juga,
            // bukan cuma lewat sewa->reservasi->penyewa.
            $table->foreignId('penyewa_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('foto_path')->nullable();
            $table->enum('status', ['baru', 'diproses', 'selesai'])->default('baru');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kerusakan');
    }
};
