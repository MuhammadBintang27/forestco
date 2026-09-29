<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalCek extends Model
{
    protected $table = 'jadwal_cek';

    protected $fillable = [
        'reservasi_id',
        'tanggal_diminta',
        'waktu_diminta',
        'catatan',
        'status',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_diminta' => 'date',
        ];
    }

    /** @return BelongsTo<Reservasi, $this> */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Persetujuan Tanggal',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'done' => 'Selesai Dicek',
            default => $this->status,
        };
    }

    public function scheduleLabel(): string
    {
        $date = $this->tanggal_diminta->translatedFormat('d F Y');

        return $this->waktu_diminta
            ? $date.', '.substr($this->waktu_diminta, 0, 5)
            : $date;
    }
}
