<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Coupons')]
class BillingCoupons extends Component
{
    public $coupons;

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $code = '';
    public ?int $discount_percent = null;
    public ?int $discount_fixed_cents = null;
    public string $applies_to = 'first_purchase';
    public ?int $max_uses = null;
    public ?string $valid_from = null;
    public ?string $valid_until = null;
    public bool $is_active = true;

    public function mount(): void
    {
        $this->loadCoupons();
    }

    protected function loadCoupons(): void
    {
        $this->coupons = Coupon::latest()->get();
    }

    protected function rules(): array
    {
        $uniqueCode = $this->editingId ? ",{$this->editingId}" : '';

        return [
            'code' => ['required', 'string', 'max:50', "unique:coupons,code{$uniqueCode}"],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'discount_fixed_cents' => ['nullable', 'integer', 'min:0'],
            'applies_to' => ['required', 'in:first_purchase,all_renewals'],
            'max_uses' => ['nullable', 'integer', 'min:0'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['boolean'],
        ];
    }

    public function newCoupon(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editCoupon(int $id): void
    {
        $coupon = Coupon::findOrFail($id);

        $this->editingId = $coupon->id;
        $this->code = $coupon->code;
        $this->discount_percent = $coupon->discount_percent;
        $this->discount_fixed_cents = $coupon->discount_fixed_cents;
        $this->applies_to = $coupon->applies_to;
        $this->max_uses = $coupon->max_uses;
        $this->valid_from = $coupon->valid_from?->format('Y-m-d');
        $this->valid_until = $coupon->valid_until?->format('Y-m-d');
        $this->is_active = $coupon->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        // Validate that at least one discount type is set
        if (!$this->discount_percent && !$this->discount_fixed_cents) {
            $this->addError('discount_percent', 'Set either percent or fixed discount');
            return;
        }

        $data = $this->validate();

        if ($this->editingId) {
            $coupon = Coupon::findOrFail($this->editingId);
            $coupon->update($data);
        } else {
            Coupon::create($data);
        }

        $this->loadCoupons();
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['is_active' => !$coupon->is_active]);
        $this->loadCoupons();
    }

    public function deleteCoupon(int $id): void
    {
        Coupon::findOrFail($id)->delete();
        $this->loadCoupons();
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'showForm', 'code', 'discount_percent', 'discount_fixed_cents', 'applies_to', 'max_uses', 'valid_from', 'valid_until', 'is_active']);
        $this->is_active = true;
        $this->applies_to = 'first_purchase';
    }

    public function render()
    {
        return view('livewire.admin.billing-coupons');
    }
}
