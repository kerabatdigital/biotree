<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reportable_type',
        'reportable_id',
        'reporter_email',
        'reason',
        'description',
        'status',
        'admin_notes',
        'handled_by',
        'handled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'handled_at' => 'datetime',
    ];

    /**
     * Reason options.
     */
    public const REASONS = [
        'phishing' => 'Phishing / Scam',
        'spam' => 'Spam',
        'inappropriate' => 'Inappropriate Content',
        'harassment' => 'Harassment / Bullying',
        'copyright' => 'Copyright Violation',
        'fraud' => 'Fraud',
        'other' => 'Other',
    ];

    /**
     * Status options.
     */
    public const STATUSES = [
        'open' => 'Open',
        'reviewed' => 'Reviewed',
        'actioned' => 'Action Taken',
        'dismissed' => 'Dismissed',
    ];

    /**
     * Get the reportable entity (Profile or Link).
     */
    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the admin who handled this report.
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Check if the report is open.
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Mark the report as reviewed.
     */
    public function markAsReviewed(int $adminId): void
    {
        $this->update([
            'status' => 'reviewed',
            'handled_by' => $adminId,
            'handled_at' => now(),
        ]);
    }

    /**
     * Dismiss the report.
     */
    public function dismiss(int $adminId, ?string $notes = null): void
    {
        $this->update([
            'status' => 'dismissed',
            'admin_notes' => $notes,
            'handled_by' => $adminId,
            'handled_at' => now(),
        ]);
    }

    /**
     * Action the report (take action on the content).
     */
    public function action(int $adminId, ?string $notes = null): void
    {
        $this->update([
            'status' => 'actioned',
            'admin_notes' => $notes,
            'handled_by' => $adminId,
            'handled_at' => now(),
        ]);

        // Take action on the reportable content
        $reportable = $this->reportable;

        if ($reportable) {
            if ($reportable instanceof Profile) {
                $reportable->update(['is_published' => false]);
            } elseif ($reportable instanceof Link) {
                $reportable->update(['is_active' => false]);
            } elseif ($reportable instanceof User) {
                $reportable->update(['status' => 'suspended']);
            }
        }
    }

    /**
     * Get the reportable display name for admin display.
     */
    public function getReportableDisplayNameAttribute(): string
    {
        $reportable = $this->reportable;

        if (!$reportable) {
            return 'Unknown';
        }

        if ($reportable instanceof Profile) {
            return $reportable->username;
        } elseif ($reportable instanceof Link) {
            return $reportable->title;
        } elseif ($reportable instanceof User) {
            return $reportable->name;
        }

        return class_basename($reportable);
    }

    /**
     * Get the reportable owner for display.
     */
    public function getReportableOwnerAttribute(): ?User
    {
        $reportable = $this->reportable;

        if (!$reportable) {
            return null;
        }

        if ($reportable instanceof Profile) {
            return $reportable->user;
        } elseif ($reportable instanceof Link) {
            return $reportable->profile?->user;
        } elseif ($reportable instanceof User) {
            return $reportable;
        }

        return null;
    }
}
