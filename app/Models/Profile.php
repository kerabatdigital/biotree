<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'display_name',
        'tagline',
        'bio',
        'avatar_path',
        'is_published',
        'is_verified',
        'theme',
        'custom_css',
        'seo_title',
        'seo_description',
        'og_image_path',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_verified' => 'boolean',
            'theme' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Public pages are resolved by username: biotree.my/{username}
     */
    public function getRouteKeyName(): string
    {
        return 'username';
    }
}
