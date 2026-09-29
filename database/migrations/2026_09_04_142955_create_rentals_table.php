<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sewa', function (Blueprint $table) {
            $table->id();
            // Unit didapat lewat reservasi->unit_id (sesuai ERD: SEWA hanya
            // berelasi ke RESERVASI).
            $table->foreignId('reservasi_id')->constrained('reservasi')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            // Sewa dibuat langsung begitu admin memverifikasi reservasi, mulai
            // dari "menunggu_pembayaran". Baru jadi "active" setelah pembayaran
            // pertama diverifikasi (sesuai ERD: PEMBAYARAN menempel ke SEWA).
            $table->enum('status', ['menunggu_pembayaran', 'active', 'ended'])->default('menunggu_pembayaran');
            $table->timestamp('pengingat_terkirim_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sewa');
    }
};
