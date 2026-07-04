<?php

namespace App\Jobs;

use App\Models\PageView;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Records a public-page view. Fired from a client beacon, then handled
 * after the response so it never slows the page down.
 */
class RecordPageView
{
    use Dispatchable;

    public function __construct(
        public int $profileId,
        public ?string $visitorHash = null,
        public ?string $country = null,
        public ?string $referrerHost = null,
        public ?string $device = null,
    ) {}

    public function handle(): void
    {
        PageView::create([
            'profile_id' => $this->profileId,
            'visitor_hash' => $this->visitorHash,
            'country' => $this->country,
            'referrer_host' => $this->referrerHost,
            'device' => $this->device,
        ]);
    }
}
