<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorit extends Model
{
    protected $table = 'favorit';

    protected $fillable = [
        'penyewa_id',
        'properti_id',
    ];

    /** @return BelongsTo<Pengguna, $this> */
    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'penyewa_id');
    }

    /** @return BelongsTo<Properti, $this> */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Properti::class, 'properti_id');
    }
}
