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
            'scroll' => 'landing-pages.scroll',
            default => abort(404),
        };

        return view($view, [
            'page' => $landingPage,
            'affiliateUrl' => $landingPage->resolvedAffiliateUrl(),
        ]);
    }
}
