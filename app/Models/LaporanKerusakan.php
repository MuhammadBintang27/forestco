<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKerusakan extends Model
{
    protected $table = 'laporan_kerusakan';

    protected $fillable = [
        'sewa_id',
        'penyewa_id',
        'judul',
        'deskripsi',
        'foto_path',
        'foto_selesai_path',
        'status',
        'catatan_admin',
    ];

    /** @return BelongsTo<Sewa, $this> */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Sewa::class, 'sewa_id');
    }

    /** @return BelongsTo<Pengguna, $this> */
    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'penyewa_id');
    }

    public function photoUrl(): ?string
    {
        return $this->foto_path ? asset('storage/'.$this->foto_path) : null;
    }

    public function photoSelesaiUrl(): ?string
    {
        return $this->foto_selesai_path ? asset('storage/'.$this->foto_selesai_path) : null;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'baru' => 'Laporan Baru',
            'diproses' => 'Sedang Ditindaklanjuti',
            'selesai' => 'Selesai',
            default => $this->status,
        };
    }
}
