<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }

    /**
     * The profile's saved theme merged over the app defaults.
     */
    public function effectiveTheme(): array
    {
        return array_merge(config('biotree.default_theme', []), $this->theme ?? []);
    }

    /**
     * Public pages are resolved by username: biotree.my/{username}
     */
    public function getRouteKeyName(): string
    {
        return 'username';
    }
}
