<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FasilitasProperti extends Model
{
    protected $table = 'fasilitas_properti';

    protected $fillable = [
        'properti_id',
        'nama',
    ];

    /** @return BelongsTo<Properti, $this> */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Properti::class, 'properti_id');
    }
}
