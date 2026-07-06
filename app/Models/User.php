<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Note: role/status/plan are intentionally NOT mass-assignable (DB defaults only)
// so a user can never escalate their own privileges via mass assignment.
#[Fillable(['name', 'email', 'phone', 'password', 'google_id', 'avatar', 'locale', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'plan_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The user's public BioTree page (biotree.my/{username}).
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function subscription(): HasOne
    {
        // A user can accumulate multiple Subscription rows over time (one per checkout
        // attempt, including abandoned/failed ones), so always resolve to the most
        // recently created row rather than an arbitrary one.
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function isAdmin(): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        // Config-driven allowlist (ADMIN_EMAILS env), matched case-insensitively.
        $adminEmails = array_map('strtolower', config('biotree.admin_emails', []));

        return in_array(strtolower($this->email), $adminEmails, true);
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isPro(): bool
    {
        return $this->plan === 'pro' && ($this->plan_expires_at === null || $this->plan_expires_at->isFuture());
    }

    /**
     * Whether the user has finished onboarding (claimed a username).
     */
    public function hasCompletedOnboarding(): bool
    {
        return $this->profile()->exists();
    }
}
