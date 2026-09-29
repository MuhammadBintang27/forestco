<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyewa_id')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('properti_id')->constrained('properti')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['penyewa_id', 'properti_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorit');
    }
};
