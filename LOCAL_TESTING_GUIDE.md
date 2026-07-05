# BioTree Billing System - Local Testing Guide

## Setup Complete ✅

Database has been migrated and seeded with:
- **Plans**: Free, Pro Monthly (RM 6), Pro Yearly (RM 40)
- **Coupons**: WELCOME20 (20% off), SAVE50 (RM 3 off), EARLYBIRD15 (15% off)

## Testing Checklist

### 1. Start the Application
```bash
./vendor/bin/sail up -d
```

Access at: http://localhost:8080

### 2. User Authentication
- [ ] Log in with test account (test@example.com / password)
  - If needed, register a new account at `/register`
  - Complete onboarding by claiming a username

### 3. User Dashboard & Navigation
- [ ] Dashboard loads at `/dashboard`
- [ ] Links editor at `/links` (displays links)
- [ ] Appearance editor at `/appearance` (shows current theme)
- [ ] Profile page at `/profile` (user account settings)

### 4. Billing Upgrade Page `/billing/upgrade`
**This is the main billing entry point**

- [ ] Page displays 3 plans: Free, Pro Monthly, Pro Yearly
- [ ] Each plan shows:
  - [ ] Plan name and description
  - [ ] Monthly/Yearly pricing toggle
  - [ ] Features list with checkmarks
  - [ ] Limits (max_links, monthly_views)
- [ ] Can select a plan by clicking "Select Plan" button
- [ ] Selected plan highlights in green
- [ ] Plan changes when toggling billing period (monthly ↔ yearly)

### 5. Coupon Application
- [ ] Input field appears when plan is selected
- [ ] Try coupon code: **WELCOME20** (20% off first purchase)
  - Should show discount applied
- [ ] Try coupon code: **SAVE50** (RM 3 fixed discount)
- [ ] Try invalid code: **INVALID999**
  - Should show error message
- [ ] Submit form with valid coupon

### 6. Checkout Flow
**Requires ToyyibPay credentials to be live**

After clicking "Proceed to Checkout" with Pro Monthly selected:

- [ ] Redirected to `/billing/checkout` with parameters:
  ```
  plan_id=2&billing_period=monthly&coupon_code=WELCOME20
  ```
- [ ] Payment session created
- [ ] Redirected to ToyyibPay payment page

**Note**: Payment gateway callback handling requires:
1. Your server must be accessible from internet (not localhost)
2. For local testing, you can skip actual payment and test the database records

### 7. Database State Verification (Local)

Instead of live payment, verify database records created:

```bash
./vendor/bin/sail artisan tinker
```

Then run:
```php
# Check subscriptions
Subscription::with('user', 'plan')->latest()->first();

# Check pending payments
Payment::where('status', 'pending')->latest()->first();

# Check coupons used
Coupon::where('code', 'WELCOME20')->first();
```

Expected:
- Subscription with status = 'pending'
- Payment with amount reduced by coupon discount
- Coupon used_count incremented

### 8. Admin Panel `/admin`
**Access requires admin role**

#### Plans Management `/admin/billing/plans`
- [ ] Table displays all 3 plans
- [ ] Can toggle "active" status
- [ ] Click "Edit" to modify plan pricing/features
- [ ] Create new plan button works
- [ ] Delete button prevents deletion if subscriptions exist

#### Subscriptions Management `/admin/billing/subscriptions`
- [ ] Lists all subscriptions (including pending ones you created)
- [ ] Can search by user name/email
- [ ] Filter by status: active, expired, cancelled
- [ ] "Change Plan" button opens modal to upgrade/downgrade
- [ ] "Cancel Subscription" option available

**Manual Admin Actions** (no charge):
- [ ] Upgrade user from Pro Monthly → Pro Yearly
- [ ] Downgrade from Pro → Free plan
- [ ] Cancel subscription
- [ ] Verify user's plan changes immediately without payment

#### Coupons Management `/admin/billing/coupons`
- [ ] Lists all 3 test coupons
- [ ] Can toggle coupon active/inactive
- [ ] Shows discount amount and usage count
- [ ] Verify "applies_to" scope (first_purchase vs all_renewals)
- [ ] Can edit max_uses and validity dates

### 9. Subscription Dashboard `/billing/subscriptions`
**Shows user their current subscription**

- [ ] Displays current plan info (if subscribed)
- [ ] Shows status: active/expired/cancelled
- [ ] Shows renewal date (next auto-renewal attempt)
- [ ] Shows auto-renewal toggle status
- [ ] Payment history table (currently empty until payment successful)
- [ ] Can upgrade to different plan
- [ ] Shows message if no active subscription

### 10. Email Notifications (via Mailpit)

Access Mailpit dashboard: http://localhost:8025

**Expected emails after successful payment**:
- [ ] SubscriptionCreatedNotification sent
  - Subject: "Welcome to BioTree Pro"
  - Shows plan name, start date, renewal date
- [ ] SubscriptionRenewalFailedNotification (on renewal failure)
- [ ] SubscriptionExpiringNotification (7 days before renewal)

View email content in Mailpit to verify formatting and content.

### 11. Queue Processing (Renewal Jobs)

Check queue worker is running:

```bash
./vendor/bin/sail logs laravel.queue
```

**Manual job dispatch for testing**:

```bash
./vendor/bin/sail artisan queue:work --once
```

This processes one job and exits. Useful for testing renewal automation without running full worker.

**Renewal automation command** (runs daily via scheduler):

```bash
./vendor/bin/sail artisan subscriptions:renew
```

This finds subscriptions expiring within 24 hours and dispatches renewal jobs.

### 12. Error Scenarios

#### Invalid Plan
- [ ] Select plan, manually modify URL to `plan_id=999`
- [ ] Should show validation error

#### Expired Coupon
- [ ] Create coupon with `valid_until` in the past
- [ ] Try using it at checkout
- [ ] Should reject with "expired" message

#### Coupon Max Uses Exceeded
- [ ] Create coupon with `max_uses=1`
- [ ] Use it once (success)
- [ ] Try using again
- [ ] Should reject with "max uses exceeded" message

#### Payment Callback Verification
- **Honest callback**: Gateway verifies MD5 hash signature
- [ ] Try callback with modified amount
- [ ] Should reject with "Invalid signature"

### 13. Feature Gating (Plan-Based Limits)

After subscription activates:

- [ ] Pro users can add unlimited links
- [ ] Free users limited to 5 links (future implementation)
- [ ] Free users can't access custom CSS editor (future: grayed out)
- [ ] User.isPro() method returns correct value

---

## Testing Timeline

| Step | Time | What to Verify |
|------|------|---|
| 1. Setup | 5min | Containers running, DB seeded |
| 2. Auth | 5min | Login works, dashboard accessible |
| 3. Plans UI | 5min | All 3 plans display correctly |
| 4. Coupons | 5min | Coupon codes apply/validate |
| 5. Checkout | 10min | Subscription/Payment records created |
| 6. Admin Ops | 5min | Manual upgrades/downgrades/cancels |
| 7. Notifications | 5min | Emails in Mailpit |
| 8. Queue | 5min | Renewal jobs process |
| 9. Errors | 10min | Invalid inputs handled gracefully |

**Total: ~55 minutes for comprehensive testing**

---

## Common Issues & Solutions

### Issue: "Plan not found"
**Solution**: Verify BillingSeeder ran successfully:
```bash
./vendor/bin/sail artisan db:seed --class=BillingSeeder
```

### Issue: ToyyibPay returns error
**Solution**: 
1. Verify credentials in `.env`: `TOYYIBPAY_SECRET`, `TOYYIBPAY_CATEGORY_CODE`
2. Callback URL must be publicly accessible (not localhost)
3. Use sandbox mode for testing first

### Issue: Queue worker not processing jobs
**Solution**:
1. Check laravel.queue container is running: `./vendor/bin/sail ps`
2. Check Redis is running: `./vendor/bin/sail redis-cli ping`
3. Monitor queue: `./vendor/bin/sail artisan queue:monitor`

### Issue: Payment notification not sent
**Solution**:
1. Check Mailpit is running on port 8025
2. Verify MAIL_MAILER=resend in .env
3. Check logs: `./vendor/bin/sail logs laravel.test`

---

## Next Steps After Local Testing

Once all tests pass:
1. ✅ Commit test seeder
2. Create test suite for billing flows
3. Set up payment provider sandbox account
4. Deploy to staging for live payment testing
5. Finally: deploy to production (VPS)

**Remember**: User explicitly requested "only deploy once all completed locally" ✅
