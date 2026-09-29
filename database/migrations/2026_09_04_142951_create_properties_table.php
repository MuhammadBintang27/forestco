<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('pengguna')->cascadeOnDelete();
            $table->enum('tipe', ['kos', 'rumah', 'ruko']);
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('alamat');
            $table->text('deskripsi')->nullable();
            // Harga, minimum sewa, ukuran & tipe kamar mandi sekarang ada di
            // tabel `unit` (mengikuti ERD: UNIT punya harga_sewa sendiri) -
            // properti hanya menyimpan data umum lokasi/deskripsi. Setiap
            // properti, termasuk rumah/ruko, selalu punya minimal 1 baris unit.
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properti');
    }
};
