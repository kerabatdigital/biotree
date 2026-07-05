<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Coupon;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        // Free plan
        Plan::create([
            'name' => 'Free',
            'slug' => 'free',
            'description' => 'Get started with BioTree free plan',
            'monthly_price_cents' => null,
            'yearly_price_cents' => null,
            'features' => [
                'Basic profile page',
                'Up to 5 links',
                'Standard themes',
                'Click analytics',
            ],
            'limits' => [
                'max_links' => 5,
                'monthly_views' => 10000,
            ],
            'is_active' => true,
        ]);

        // Pro Monthly plan - RM 6/month = 600 cents
        Plan::create([
            'name' => 'Pro Monthly',
            'slug' => 'pro_monthly',
            'description' => 'Unlimited links and custom branding',
            'monthly_price_cents' => 600,
            'yearly_price_cents' => null,
            'features' => [
                'Everything in Free',
                'Unlimited links',
                'Custom CSS',
                'Custom domain support',
                'Advanced analytics',
                'Link scheduling',
                'No BioTree branding',
            ],
            'limits' => [
                'max_links' => 999,
                'monthly_views' => 1000000,
            ],
            'is_active' => true,
        ]);

        // Pro Yearly plan - RM 40/year = 4000 cents
        Plan::create([
            'name' => 'Pro Yearly',
            'slug' => 'pro_yearly',
            'description' => 'Save 33% with annual billing',
            'monthly_price_cents' => null,
            'yearly_price_cents' => 4000,
            'features' => [
                'Everything in Free',
                'Unlimited links',
                'Custom CSS',
                'Custom domain support',
                'Advanced analytics',
                'Link scheduling',
                'No BioTree branding',
            ],
            'limits' => [
                'max_links' => 999,
                'monthly_views' => 1000000,
            ],
            'is_active' => true,
        ]);

        // Sample coupons for testing
        Coupon::create([
            'code' => 'WELCOME20',
            'discount_percent' => 20,
            'discount_fixed_cents' => null,
            'applies_to' => 'first_purchase',
            'max_uses' => 100,
            'used_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(3),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'SAVE50',
            'discount_fixed_cents' => 300, // RM 3 off
            'discount_percent' => null,
            'applies_to' => 'all_renewals',
            'max_uses' => null, // unlimited
            'used_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addYear(),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'EARLYBIRD15',
            'discount_percent' => 15,
            'discount_fixed_cents' => null,
            'applies_to' => 'first_purchase',
            'max_uses' => 50,
            'used_count' => 0,
            'valid_from' => now()->subDays(30),
            'valid_until' => now()->addDays(30),
            'is_active' => true,
        ]);

        $this->command->info('Billing data seeded successfully!');
        $this->command->info('Plans: Free, Pro Monthly (RM 6), Pro Yearly (RM 40)');
        $this->command->info('Coupons: WELCOME20 (20% off), SAVE50 (RM 3 off), EARLYBIRD15 (15% off)');
    }
}
