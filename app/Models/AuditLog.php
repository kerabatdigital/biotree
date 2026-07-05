<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_id',
        'action',
        'target_type',
        'target_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Common admin actions.
     */
    public const ACTIONS = [
        // User actions
        'suspend_user' => 'Suspended user',
        'restore_user' => 'Restored user',
        'make_admin' => 'Made user an admin',
        'remove_admin' => 'Removed admin role',
        'delete_user' => 'Deleted user',

        // Profile actions
        'unpublish_profile' => 'Unpublished profile',
        'publish_profile' => 'Published profile',
        'verify_profile' => 'Verified profile',
        'unverify_profile' => 'Unverified profile',
        'delete_profile' => 'Deleted profile',

        // Link actions
        'delete_link' => 'Deleted link',
        'disable_link' => 'Disabled link',
        'enable_link' => 'Enabled link',

        // Report actions
        'dismiss_report' => 'Dismissed report',
        'action_report' => 'Took action on report',
        'review_report' => 'Marked report as reviewed',

        // Settings actions
        'update_settings' => 'Updated settings',
        'toggle_feature' => 'Toggled feature flag',
        'update_reserved_usernames' => 'Updated reserved usernames',
    ];

    /**
     * Get the admin who performed the action.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the target model instance.
     */
    public function getTargetAttribute(): ?Model
    {
        return $this->target_type::find($this->target_id);
    }

    /**
     * Log an admin action.
     */
    public static function log(
        int $adminId,
        string $action,
        Model $target,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): self {
        return static::create([
            'admin_id' => $adminId,
            'action' => $action,
            'target_type' => get_class($target),
            'target_id' => $target->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'created_at' => now(),
        ]);
    }

    /**
     * Get the action display name.
     */
    public function getActionDisplayAttribute(): string
    {
        return self::ACTIONS[$this->action] ?? $this->action;
    }

    /**
     * Scope to filter by action type.
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by target type.
     */
    public function scopeTargetType($query, string $type)
    {
        return $query->where('target_type', $type);
    }

    /**
     * Scope to filter by target id.
     */
    public function scopeForTarget($query, string $type, int $id)
    {
        return $query->where('target_type', $type)->where('target_id', $id);
    }

    /**
     * Scope to filter recent logs.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
