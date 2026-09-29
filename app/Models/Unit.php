<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $table = 'unit';

    protected $fillable = [
        'properti_id',
        'kode',
        'harga',
        'harga_tahunan',
        'ukuran_kamar',
        'tipe_kamar_mandi',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'harga_tahunan' => 'integer',
        ];
    }

    /** @return BelongsTo<Properti, $this> */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Properti::class, 'properti_id');
    }

    /** @return HasMany<Reservasi, $this> */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservasi::class, 'unit_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'tersedia';
    }

    /**
     * Kos punya banyak kamar dengan kode asli (A1, B2, dst) yang perlu
     * ditampilkan ke pengguna; rumah/ruko cuma punya 1 unit placeholder
     * (kode "-") yang mewakili properti itu sendiri, jadi tidak perlu (dan
     * tidak masuk akal) ditampilkan sebagai "Kamar -".
     */
    public function hasDisplayableKode(): bool
    {
        return $this->property->isKos();
    }

    public function effectivePrice(): int
    {
        return $this->harga;
    }

    /**
     * Yearly package price for this unit, if any - null if no yearly
     * package is offered.
     */
    public function effectiveYearlyPrice(): ?int
    {
        return $this->harga_tahunan;
    }

    /**
     * Total price for a given duration (6, 12, or 24 bulan) - uses the
     * yearly package rate when one exists and the duration is a whole
     * number of years (12 or 24 = 1x/2x harga_tahunan), otherwise falls
     * back to the monthly rate times the number of months.
     */
    public function priceForDuration(int $months): int
    {
        $yearly = $this->effectiveYearlyPrice();

        if ($yearly && $months % 12 === 0) {
            return $yearly * intdiv($months, 12);
        }

        return $this->effectivePrice() * $months;
    }

    /**
     * Harga utama yang ditampilkan - kos per bulan, rumah/ruko per tahun
     * (lihat Properti::primaryPrice()/primaryPriceUnit()).
     */
    public function primaryPrice(): int
    {
        return $this->property->isKos() ? $this->effectivePrice() : ($this->effectiveYearlyPrice() ?? $this->effectivePrice());
    }

    public function primaryPriceUnit(): string
    {
        return $this->property->isKos() ? 'bulan' : 'tahun';
    }
}
