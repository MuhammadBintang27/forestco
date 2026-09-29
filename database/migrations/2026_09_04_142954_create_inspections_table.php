<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_cek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservasi_id')->constrained('reservasi')->cascadeOnDelete();
            $table->date('tanggal_diminta');
            $table->time('waktu_diminta')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'done'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_cek');
    }
};
