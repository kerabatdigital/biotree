<?php

namespace App\Jobs;

use App\Models\Link;
use App\Models\LinkClick;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Records a link click off the request's critical path
 * (dispatched via dispatchAfterResponse, so the redirect stays instant).
 */
class RecordLinkClick
{
    use Dispatchable;

    public function __construct(
        public int $linkId,
        public int $profileId,
        public ?string $country = null,
        public ?string $referrerHost = null,
        public ?string $device = null,
    ) {}

    public function handle(): void
    {
        LinkClick::create([
            'link_id' => $this->linkId,
            'profile_id' => $this->profileId,
            'country' => $this->country,
            'referrer_host' => $this->referrerHost,
            'device' => $this->device,
        ]);

        Link::whereKey($this->linkId)->increment('clicks_count');
    }
}
