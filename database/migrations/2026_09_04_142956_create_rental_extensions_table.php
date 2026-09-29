<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perpanjangan_sewa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sewa_id')->constrained('sewa')->cascadeOnDelete();
            $table->unsignedInteger('durasi_diminta_bulan');
            $table->date('tanggal_selesai_baru')->nullable();
            $table->enum('status', ['requested', 'awaiting_payment', 'approved', 'rejected'])->default('requested');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perpanjangan_sewa');
    }
};
