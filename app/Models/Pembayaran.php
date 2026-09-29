<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'sewa_id',
        'perpanjangan_sewa_id',
        'jumlah',
        'bukti_path',
        'status',
        'catatan_admin',
        'jatuh_tempo',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'jatuh_tempo' => 'date',
        ];
    }

    /** @return BelongsTo<Sewa, $this> */
    public function sewa(): BelongsTo
    {
        return $this->belongsTo(Sewa::class, 'sewa_id');
    }

    /** @return BelongsTo<PerpanjanganSewa, $this> */
    public function rentalExtension(): BelongsTo
    {
        return $this->belongsTo(PerpanjanganSewa::class, 'perpanjangan_sewa_id');
    }

    public function proofUrl(): string
    {
        return asset('storage/'.$this->bukti_path);
    }

    public function proofIsImage(): bool
    {
        return in_array(strtolower(pathinfo($this->bukti_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'], true);
    }

    /**
     * The tenant who this payment belongs to - always reachable via the
     * sewa it's attached to, whether it's the first payment for a fresh
     * reservation or a payment for a rental extension.
     */
    public function penyewa(): ?Pengguna
    {
        return $this->sewa?->reservation?->penyewa;
    }
}
