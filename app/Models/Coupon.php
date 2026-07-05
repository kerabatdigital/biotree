<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_percent',
        'discount_fixed_cents',
        'applies_to',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        if ($this->valid_from && $this->valid_from->isFuture()) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(int $amountCents): int
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->discount_percent) {
            return (int) ($amountCents * $this->discount_percent / 100);
        }

        if ($this->discount_fixed_cents) {
            return min($this->discount_fixed_cents, $amountCents);
        }

        return 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
