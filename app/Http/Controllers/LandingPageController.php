<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function show(LandingPage $landingPage): View
    {
        abort_unless($landingPage->is_active, 404);

        $view = match ($landingPage->type->value) {
            'standard' => 'landing-pages.standard',
            'popup' => 'landing-pages.popup',
            'scroll' => 'landing-pages.compare',
            default => abort(404),
        };

        $data = [
            'page' => $landingPage,
            'affiliateUrl' => $landingPage->resolvedAffiliateUrl(),
        ];

        if ($landingPage->type->value === 'scroll') {
            $data['compare'] = is_array($landingPage->sections) && isset($landingPage->sections['rows'])
                ? $landingPage->sections
                : app(\App\Services\LandingAiService::class)->fallbackCompare([
                    'title' => $landingPage->title,
                    'description' => $landingPage->meta_description ?? '',
                    'affiliate_url' => $landingPage->resolvedAffiliateUrl(),
                ]);
        }

        return view($view, $data);
    }
}
