<?php

namespace App\Http\Controllers;

use App\Models\Profile;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $urls = [
            ['loc' => url('/'), 'priority' => '1.0'],
            ['loc' => url('/pricing'), 'priority' => '0.9'],
            ['loc' => url('/terms'), 'priority' => '0.3'],
            ['loc' => url('/privacy'), 'priority' => '0.3'],
        ];

        Profile::where('is_published', true)
            ->orderByDesc('updated_at')
            ->limit(10000)
            ->get(['username', 'updated_at'])
            ->each(function (Profile $p) use (&$urls) {
                $urls[] = [
                    'loc' => url('/'.$p->username),
                    'lastmod' => $p->updated_at?->toAtomString(),
                    'priority' => '0.8',
                ];
            });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $u) {
            $xml .= '  <url><loc>'.e($u['loc']).'</loc>';
            if (! empty($u['lastmod'])) {
                $xml .= '<lastmod>'.$u['lastmod'].'</lastmod>';
            }
            $xml .= '<changefreq>weekly</changefreq><priority>'.$u['priority'].'</priority></url>'."\n";
        }
        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
