<?php

namespace App\Http\Middleware;

use App\Services\AffiliateService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackReferral
{
    public function __construct(private AffiliateService $affiliateService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->affiliateService->trackReferral($request);

        return $next($request);
    }
}
