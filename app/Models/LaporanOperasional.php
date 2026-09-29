<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanOperasional extends Model
{
    protected $table = 'laporan_operasional';

    protected $fillable = [
        'admin_id',
        'jenis_laporan',
        'format_berkas',
        'periode_mulai',
        'periode_selesai',
        'tanggal_unduh',
    ];

    protected function casts(): array
    {
        return [
            'periode_mulai' => 'date',
            'periode_selesai' => 'date',
            'tanggal_unduh' => 'datetime',
        ];
    }

    /** @return BelongsTo<Pengguna, $this> */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'admin_id');
    }

    public function jenisLabel(): string
    {
        return match ($this->jenis_laporan) {
            'reservasi' => 'Laporan Reservasi & Pembayaran',
            default => $this->jenis_laporan,
        };
    }
}
