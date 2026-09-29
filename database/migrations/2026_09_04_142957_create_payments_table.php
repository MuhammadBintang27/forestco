<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            // Sesuai ERD: PEMBAYARAN menempel ke SEWA (bukan ke reservasi
            // langsung) - sewa dibuat lebih dulu (status menunggu_pembayaran)
            // begitu reservasi diverifikasi, baru penyewa membayar ke sewa itu.
            $table->foreignId('sewa_id')->constrained('sewa')->cascadeOnDelete();
            // Diisi hanya kalau pembayaran ini untuk melunasi perpanjangan sewa
            // (bukan pembayaran pertama) - dipakai untuk menandai baris
            // perpanjangan_sewa mana yang mau diselesaikan lewat pembayaran ini.
            $table->foreignId('perpanjangan_sewa_id')->nullable()->constrained('perpanjangan_sewa')->cascadeOnDelete();
            $table->unsignedBigInteger('jumlah');
            $table->string('bukti_path');
            $table->enum('status', ['review', 'verified', 'rejected'])->default('review');
            $table->text('catatan_admin')->nullable();
            $table->date('jatuh_tempo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
