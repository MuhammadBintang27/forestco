<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Properti extends Model
{
    protected $table = 'properti';

    protected $fillable = [
        'admin_id',
        'tipe',
        'nama',
        'slug',
        'alamat',
        'deskripsi',
        'status',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<Pengguna, $this> */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'admin_id');
    }

    /** @return HasMany<FotoProperti, $this> */
    public function photos(): HasMany
    {
        return $this->hasMany(FotoProperti::class, 'properti_id')->orderBy('urutan');
    }

    /** @return HasMany<FasilitasProperti, $this> */
    public function facilities(): HasMany
    {
        return $this->hasMany(FasilitasProperti::class, 'properti_id');
    }

    /** @return HasMany<Unit, $this> */
    public function rooms(): HasMany
    {
        return $this->hasMany(Unit::class, 'properti_id');
    }

    /**
     * Reservasi tidak lagi punya properti_id langsung (sesuai ERD) - didapat
     * lewat unit_id, jadi relasi ini "menembus" tabel unit.
     *
     * @return HasManyThrough<Reservasi, Unit, $this>
     */
    public function reservations(): HasManyThrough
    {
        return $this->hasManyThrough(Reservasi::class, Unit::class, 'properti_id', 'unit_id');
    }

    /** @return HasMany<Favorit, $this> */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorit::class, 'properti_id');
    }

    public function isFavoritedBy(?Pengguna $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->favorites()->where('penyewa_id', $user->id)->exists();
    }

    public function isKos(): bool
    {
        return $this->tipe === 'kos';
    }

    /**
     * Properti rumah/ruko selalu punya tepat 1 unit yang mewakili properti
     * itu sendiri (dibuat otomatis saat properti dibuat/diubah) - dipakai
     * untuk baca/tulis harga & atribut fisik lewat unit itu.
     */
    public function soleUnit(): ?Unit
    {
        return $this->isKos() ? null : $this->rooms->first();
    }

    /**
     * Lowest currently-available price to display on catalog cards.
     */
    public function displayPrice(): int
    {
        $room = $this->rooms
            ->where('status', 'tersedia')
            ->sortBy(fn (Unit $room) => $room->effectivePrice())
            ->first() ?? $this->rooms->first();

        return $room?->effectivePrice() ?? 0;
    }

    /**
     * Lowest currently-available yearly price to display alongside the
     * monthly one, if the admin has set one - null if not offered.
     */
    public function displayYearlyPrice(): ?int
    {
        return $this->rooms
            ->where('status', 'tersedia')
            ->map(fn (Unit $room) => $room->effectiveYearlyPrice())
            ->filter()
            ->sort()
            ->first();
    }

    public function availableRoomsCount(): int
    {
        return $this->rooms->where('status', 'tersedia')->count();
    }

    /**
     * Harga utama yang ditampilkan di katalog/kartu admin - kos per bulan
     * (harga per kamar), rumah/ruko per tahun (satu-satunya harga yang
     * diinput admin, karena durasi sewa rumah/ruko selalu kelipatan tahun).
     */
    public function primaryPrice(): int
    {
        return $this->isKos() ? $this->displayPrice() : ($this->displayYearlyPrice() ?? $this->displayPrice());
    }

    public function primaryPriceUnit(): string
    {
        return $this->isKos() ? 'bulan' : 'tahun';
    }

    /**
     * Whether this property currently has something to offer a new tenant -
     * for kos, at least one free room; for rumah/ruko, its sole unit free.
     */
    public function isAvailable(): bool
    {
        return $this->availableRoomsCount() > 0;
    }

    /**
     * Short "city/area" label taken from the tail of the free-text address,
     * used to populate the location filter on the catalog page.
     */
    public function locationLabel(): string
    {
        $parts = array_map('trim', explode(',', $this->alamat));

        return end($parts) ?: $this->alamat;
    }

    /**
     * One-line summary of standout facts shown on catalog cards, e.g.
     * "12 Kamar Tersedia · WiFi · Kamar Mandi Dalam".
     */
    public function highlightLine(): string
    {
        $parts = [];

        if ($this->isKos()) {
            $parts[] = $this->availableRoomsCount().' Kamar Tersedia';
        }

        foreach ($this->facilities->take($this->isKos() ? 2 : 3) as $facility) {
            $parts[] = $facility->nama;
        }

        return implode(' · ', $parts);
    }
}
