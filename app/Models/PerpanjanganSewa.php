<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerpanjanganSewa extends Model
{
    protected $table = 'perpanjangan_sewa';

    protected $fillable = [
        'sewa_id',
        'durasi_diminta_bulan',
        'tanggal_selesai_baru',
        'status',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'durasi_diminta_bulan' => 'integer',
            'tanggal_selesai_baru' => 'date',
        ];
    }

    /** @return BelongsTo<Sewa, $this> */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Sewa::class, 'sewa_id');
    }

    /** @return HasMany<Pembayaran, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'perpanjangan_sewa_id');
    }

    public function latestPayment(): ?Pembayaran
    {
        return $this->payments()->latest()->first();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'requested' => 'Menunggu Persetujuan',
            'awaiting_payment' => 'Menunggu Pembayaran',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => $this->status,
        };
    }
}
