<?php

namespace App\Http\Controllers;

use App\Jobs\RecordPageView;
use App\Models\Profile;
use App\Support\Visitor;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    /**
     * Page-view beacon. Kept off the cached HTML path so public pages
     * stay fully edge-cacheable while views are still counted.
     */
    public function view(Request $request)
    {
        $validated = $request->validate([
            'profile' => ['required', 'integer'],
        ]);

        $profile = Profile::where('id', $validated['profile'])
            ->where('is_published', true)
            ->first();

        if ($profile) {
            RecordPageView::dispatchAfterResponse(
                $profile->id,
                Visitor::hash($request, $profile->id),
                Visitor::country($request),
                Visitor::referrerHost($request),
                Visitor::device($request),
            );
        }

        return response()->noContent();
    }
}
