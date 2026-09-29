<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = [
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik_rekening',
        'no_wa_admin',
    ];

    /**
     * Site-wide settings are stored as a single row. Fetch (or lazily create) it.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
