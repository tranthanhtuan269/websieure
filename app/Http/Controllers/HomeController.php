<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Theme;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['themes' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        $featuredThemes = Theme::query()
            ->with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        $latestThemes = Theme::query()
            ->with('category')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact('categories', 'featuredThemes', 'latestThemes'));
    }
}
