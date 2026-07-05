<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\ThemeBuilder;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'display_name',
        'tagline',
        'bio',
        'avatar_path',
        'theme',
        'is_verified',
    ];

    protected $casts = [
        'theme' => 'array',
        'is_verified' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class)->orderBy('position');
    }

    public function reports(): MorphTo
    {
        return $this->morphTo();
    }

    public function username(): string
    {
        return $this->username ?? "user{$this->id}";
    }

    public function avatarUrl(): ?string
    {
        if (!$this->avatar_path) {
            return null;
        }

        return asset("storage/{$this->avatar_path}");
    }

    public function effectiveTheme(): array
    {
        return array_merge(config('biotree.default_theme'), $this->theme ?? []);
    }

    public function publicTheme(): array
    {
        return ThemeBuilder::build($this->effectiveTheme());
    }

    public function isOwner(?User $user): bool
    {
        return $user && $user->id === $this->user_id;
    }
}
