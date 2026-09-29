<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyewa_id')->constrained('pengguna')->cascadeOnDelete();
            // Properti didapat lewat unit->properti_id (sesuai ERD: RESERVASI
            // hanya berelasi ke UNIT). Setiap properti - termasuk rumah/ruko -
            // selalu punya minimal 1 unit, jadi unit_id selalu wajib diisi.
            $table->foreignId('unit_id')->constrained('unit')->cascadeOnDelete();
            // Durasi sewa dibatasi ke 3 pilihan tetap: 6, 12 (1 tahun), atau
            // 24 (2 tahun) bulan - divalidasi di form request, bukan free input.
            $table->unsignedInteger('durasi_bulan');
            $table->text('catatan')->nullable();
            // Status reservasi berhenti di sini - setelah "verified", progres
            // selanjutnya (menunggu bayar, aktif, selesai) dibaca dari data
            // sewa & pembayaran terkait, bukan menimpa kolom ini lagi.
            $table->enum('status', [
                'pending',
                'verified',
                'rejected',
                'dibatalkan',
            ])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};
