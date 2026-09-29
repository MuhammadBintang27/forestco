<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sewa extends Model
{
    protected $table = 'sewa';

    protected $fillable = [
        'reservasi_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'pengingat_terkirim_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'pengingat_terkirim_pada' => 'datetime',
        ];
    }

    /** @return BelongsTo<Reservasi, $this> */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }

    /**
     * Unit tidak lagi jadi FK langsung di sewa (sesuai ERD) - didapat lewat
     * reservasi. Eager-load lewat `reservation.room`, bukan `room`.
     */
    protected function room(): Attribute
    {
        return Attribute::get(fn () => $this->reservation?->room);
    }

    /** @return HasMany<PerpanjanganSewa, $this> */
    public function extensions(): HasMany
    {
        return $this->hasMany(PerpanjanganSewa::class, 'sewa_id');
    }

    /** @return HasMany<Pembayaran, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'sewa_id');
    }

    /** @return HasMany<LaporanKerusakan, $this> */
    public function damageReports(): HasMany
    {
        return $this->hasMany(LaporanKerusakan::class, 'sewa_id');
    }

    public function daysRemaining(): int
    {
        return now()->startOfDay()->diffInDays($this->tanggal_selesai, false);
    }
}
