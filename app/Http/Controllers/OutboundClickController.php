<?php

namespace App\Http\Controllers;

use App\Jobs\RecordLinkClick;
use App\Models\Link;
use App\Support\Visitor;
use Illuminate\Http\Request;

class OutboundClickController extends Controller
{
    /**
     * Log the click (after the response) and redirect to the target URL.
     * The Link is resolved by its ULID (see Link::getRouteKeyName).
     */
    public function __invoke(Request $request, Link $link)
    {
        abort_if(blank($link->url), 404);

        RecordLinkClick::dispatchAfterResponse(
            $link->id,
            $link->profile_id,
            Visitor::country($request),
            Visitor::referrerHost($request),
            Visitor::device($request),
        );

        return redirect()->away($link->url);
    }
}
