<?php

namespace App\Models;

use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    /** @use HasFactory<PenggunaFactory> */
    use HasFactory, Notifiable;

    protected $table = 'pengguna';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'no_telepon',
        'peran',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function isPenyewa(): bool
    {
        return $this->peran === 'penyewa';
    }

    /** @return HasMany<Properti, $this> */
    public function properties(): HasMany
    {
        return $this->hasMany(Properti::class, 'admin_id');
    }

    /** @return HasMany<Reservasi, $this> */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservasi::class, 'penyewa_id');
    }

    /** @return BelongsToMany<Properti, $this> */
    public function favoriteProperties(): BelongsToMany
    {
        return $this->belongsToMany(Properti::class, 'favorit', 'penyewa_id', 'properti_id')->withTimestamps();
    }

    /**
     * Two-letter avatar initials, e.g. "Rizky Ananda" -> "RA".
     */
    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->nama)) ?: [];

        $initials = collect($words)->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('');

        return $initials !== '' ? $initials : '?';
    }
}
