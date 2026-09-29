<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservasi extends Model
{
    protected $table = 'reservasi';

    /**
     * Durasi sewa dibatasi ke 3 pilihan tetap (sesuai ERD: durasi_sewa
     * adalah satu nilai, bukan rentang bebas) - 6 bulan, 1 tahun, 2 tahun.
     */
    public const DURASI_OPTIONS = [
        6 => '6 Bulan',
        12 => '1 Tahun',
        24 => '2 Tahun',
    ];

    /**
     * Pilihan durasi tergantung tipe properti - tidak ada lagi field
     * "minimum sewa" per unit, cukup diturunkan dari tipe: kos boleh mulai
     * 6 bulan, rumah/ruko cuma kelipatan tahun (1 atau 2 tahun).
     */
    public static function durasiOptionsFor(string $tipe): array
    {
        if ($tipe === 'kos') {
            return self::DURASI_OPTIONS;
        }

        return array_filter(self::DURASI_OPTIONS, fn ($bulan) => $bulan % 12 === 0, ARRAY_FILTER_USE_KEY);
    }

    protected $fillable = [
        'penyewa_id',
        'unit_id',
        'durasi_bulan',
        'catatan',
        'status',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'durasi_bulan' => 'integer',
        ];
    }

    /** @return BelongsTo<Pengguna, $this> */
    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'penyewa_id');
    }

    /** @return BelongsTo<Unit, $this> */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Properti tidak lagi jadi FK langsung (sesuai ERD) - didapat lewat
     * unit. Eager-load lewat `room.property`, bukan `property`.
     */
    protected function property(): Attribute
    {
        return Attribute::get(fn () => $this->room?->property);
    }

    /** @return HasMany<JadwalCek, $this> */
    public function inspections(): HasMany
    {
        return $this->hasMany(JadwalCek::class, 'reservasi_id');
    }

    /** @return HasOne<Sewa, $this> */
    public function rental(): HasOne
    {
        return $this->hasOne(Sewa::class, 'reservasi_id');
    }

    public function latestInspection(): ?JadwalCek
    {
        return $this->inspections()->latest()->first();
    }

    public function latestPayment(): ?Pembayaran
    {
        return $this->rental?->payments()->latest()->first();
    }

    public function totalAmount(): int
    {
        return $this->room->priceForDuration($this->durasi_bulan);
    }

    /**
     * The raw `status` column only ever holds pending/verified/rejected/dibatalkan
     * - once verified, it stays verified for good. Everything past that point
     * (menunggu bayar, resmi aktif, sudah selesai) is read off the related
     * sewa/pembayaran records instead of overwriting this column. This is the
     * richer, "what's really going on" status used for display everywhere.
     */
    public function displayStatus(): string
    {
        if ($this->status !== 'verified') {
            return $this->status;
        }

        if (! $this->rental || $this->rental->status === 'menunggu_pembayaran') {
            return $this->latestPayment()?->status === 'review' ? 'awaiting_payment' : 'verified';
        }

        return $this->rental->status === 'active' ? 'active' : 'ended';
    }

    public function statusLabel(): string
    {
        return match ($this->displayStatus()) {
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan Penyewa',
            'awaiting_payment' => 'Menunggu Verifikasi Pembayaran',
            'active' => 'Aktif Menyewa',
            'ended' => 'Selesai',
            default => $this->status,
        };
    }

    public function nomor(): string
    {
        return '#RSV-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Whether the requested unit is still available to approve.
     */
    public function isUnitStillAvailable(): bool
    {
        return $this->room->isAvailable();
    }
}
