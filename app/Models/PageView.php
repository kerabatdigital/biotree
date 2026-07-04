<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'profile_id',
        'visitor_hash',
        'country',
        'referrer_host',
        'device',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
