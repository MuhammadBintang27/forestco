<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoProperti extends Model
{
    protected $table = 'foto_properti';

    protected $fillable = [
        'properti_id',
        'path',
        'urutan',
    ];

    /** @return BelongsTo<Properti, $this> */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Properti::class, 'properti_id');
    }

    public function url(): string
    {
        return asset('storage/'.$this->path);
    }
}
