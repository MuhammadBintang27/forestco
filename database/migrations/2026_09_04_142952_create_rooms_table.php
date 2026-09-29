<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('properti_id')->constrained('properti')->cascadeOnDelete();
            $table->string('kode');
            // `harga` = harga sewa bulanan (wajib, sesuai ERD unit.harga_sewa).
            // `harga_tahunan` = harga sendiri untuk paket tahunan (opsional,
            // bukan hasil kali otomatis dari bulanan, supaya admin bebas kasih
            // diskon). Paket 2 tahun dihitung otomatis `harga_tahunan x 2`.
            $table->unsignedBigInteger('harga');
            $table->unsignedBigInteger('harga_tahunan')->nullable();
            // Minimum sewa tidak disimpan per unit - durasi yang ditawarkan
            // sudah otomatis mengikuti tipe properti (lihat Reservasi::durasiOptionsFor()):
            // kos boleh 6/12/24 bulan, rumah/ruko cuma kelipatan tahun (12/24).
            $table->string('ukuran_kamar')->nullable();
            $table->string('tipe_kamar_mandi')->nullable();
            $table->enum('status', ['tersedia', 'terisi'])->default('tersedia');
            $table->timestamps();

            $table->unique(['properti_id', 'kode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit');
    }
};
