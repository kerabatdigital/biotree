<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Privacy-friendly visitor metadata for analytics — no raw IPs stored.
 */
class Visitor
{
    public static function country(Request $request): ?string
    {
        $c = $request->headers->get('CF-IPCountry'); // provided by Cloudflare in production
        return ($c && $c !== 'XX' && strlen($c) === 2) ? strtoupper($c) : null;
    }

    public static function referrerHost(Request $request): ?string
    {
        $ref = $request->headers->get('referer');
        if (! $ref) {
            return null;
        }

        $host = parse_url($ref, PHP_URL_HOST);

        // Ignore self-referrals.
        return ($host && $host !== $request->getHost()) ? $host : null;
    }

    public static function device(Request $request): string
    {
        $ua = (string) $request->userAgent();

        if (preg_match('/iPad|Tablet|PlayBook|Silk/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/Mobile|Android|iPhone|iPod|Opera Mini|IEMobile/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * A per-day, non-reversible visitor fingerprint for unique counts.
     */
    public static function hash(Request $request, string|int $salt = ''): string
    {
        return hash('sha256', implode('|', [
            $request->ip(),
            $request->userAgent(),
            now()->toDateString(),
            (string) $salt,
            (string) config('app.key'),
        ]));
    }
}
