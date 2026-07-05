<?php

namespace App\Livewire\App;

use App\Models\Link;
use App\Models\LinkClick;
use App\Models\PageView;
use App\Models\Profile;
use App\Services\QrCodeService;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Analytics extends Component
{
    public int $range = 28;

    public function setRange(int $days): void
    {
        $this->range = in_array($days, [7, 28, 90], true) ? $days : 28;
    }

    protected function profile(): Profile
    {
        return auth()->user()->profile;
    }

    protected function since(): Carbon
    {
        return now()->subDays($this->range - 1)->startOfDay();
    }

    protected function views()
    {
        return PageView::where('profile_id', $this->profile()->id)
            ->where('created_at', '>=', $this->since());
    }

    protected function clicks()
    {
        return LinkClick::where('profile_id', $this->profile()->id)
            ->where('created_at', '>=', $this->since());
    }

    public function getStatsProperty(): array
    {
        $views = (clone $this->views())->count();
        $uniques = (clone $this->views())->distinct()->count('visitor_hash');
        $clicks = (clone $this->clicks())->count();

        return [
            'views' => $views,
            'uniques' => $uniques,
            'clicks' => $clicks,
            'ctr' => $views > 0 ? round($clicks / $views * 100, 1) : 0.0,
        ];
    }

    public function getSeriesProperty(): array
    {
        $viewsByDay = (clone $this->views())->selectRaw('DATE(created_at) d, COUNT(*) c')->groupBy('d')->pluck('c', 'd');
        $clicksByDay = (clone $this->clicks())->selectRaw('DATE(created_at) d, COUNT(*) c')->groupBy('d')->pluck('c', 'd');

        $since = $this->since();
        $days = [];
        for ($i = 0; $i < $this->range; $i++) {
            $date = $since->copy()->addDays($i)->toDateString();
            $days[] = [
                'date' => $date,
                'views' => (int) ($viewsByDay[$date] ?? 0),
                'clicks' => (int) ($clicksByDay[$date] ?? 0),
            ];
        }

        return $days;
    }

    public function getChartProperty(): array
    {
        $series = $this->series;
        $n = count($series);
        $w = 600;
        $h = 150;
        $pad = 8;
        $max = max(1, max(array_merge(array_column($series, 'views'), array_column($series, 'clicks'))));

        $coords = function (string $key) use ($series, $n, $w, $h, $pad, $max) {
            $pts = [];
            foreach ($series as $i => $d) {
                $x = $n > 1 ? round($i / ($n - 1) * $w, 1) : 0;
                $y = round($h - ($d[$key] / $max) * ($h - $pad * 2) - $pad, 1);
                $pts[] = "$x $y";
            }

            return $pts;
        };

        $viewsLine = 'M '.implode(' L ', $coords('views'));

        return [
            'w' => $w,
            'h' => $h,
            'max' => $max,
            'first' => $series[0]['date'] ?? null,
            'last' => $series[$n - 1]['date'] ?? null,
            'viewsLine' => $viewsLine,
            'viewsArea' => $viewsLine." L $w $h L 0 $h Z",
            'clicksLine' => 'M '.implode(' L ', $coords('clicks')),
        ];
    }

    public function getTopLinksProperty()
    {
        $counts = (clone $this->clicks())
            ->selectRaw('link_id, COUNT(*) c')
            ->groupBy('link_id')
            ->orderByDesc('c')
            ->limit(8)
            ->pluck('c', 'link_id');

        if ($counts->isEmpty()) {
            return collect();
        }

        $links = Link::whereIn('id', $counts->keys())->get()->keyBy('id');
        $views = $this->stats['views'];
        $top = $counts->max();

        return $counts->map(fn ($c, $id) => [
            'title' => $links[$id]->title ?? 'Deleted link',
            'icon' => $links[$id]->icon ?? null,
            'clicks' => $c,
            'ctr' => $views > 0 ? round($c / $views * 100) : 0,
            'share' => $top > 0 ? round($c / $top * 100) : 0,
        ])->values();
    }

    protected function breakdown(string $column, ?string $nullLabel = null)
    {
        $rows = (clone $this->views())
            ->selectRaw("$column v, COUNT(*) c")
            ->when($nullLabel === null, fn ($q) => $q->whereNotNull($column))
            ->groupBy('v')
            ->orderByDesc('c')
            ->limit(5)
            ->get();

        $top = (int) ($rows->max('c') ?: 1);

        return $rows->map(fn ($r) => [
            'label' => $r->v ?: $nullLabel,
            'count' => (int) $r->c,
            'share' => round($r->c / $top * 100),
        ]);
    }

    public function getCountriesProperty()
    {
        return $this->breakdown('country');
    }

    public function getReferrersProperty()
    {
        return $this->breakdown('referrer_host', 'Direct');
    }

    public function getDevicesProperty()
    {
        return $this->breakdown('device');
    }

    public function render()
    {
        $publicUrl = url($this->profile()->username);

        return view('livewire.app.analytics', [
            'stats' => $this->stats,
            'chart' => $this->chart,
            'topLinks' => $this->topLinks,
            'countries' => $this->countries,
            'referrers' => $this->referrers,
            'devices' => $this->devices,
            'profileUsername' => $this->profile()->username,
            'publicUrl' => $publicUrl,
            'qrUrl' => QrCodeService::generate($publicUrl),
            'qrDownloadUrl' => QrCodeService::download($publicUrl),
        ]);
    }
}
