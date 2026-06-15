<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThemeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Theme::query()
            ->with('category')
            ->where('is_active', true);

        if ($request->filled('q')) {
            $q = $request->string('q')->trim();
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('tagline', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($c) => $c->where('slug', $request->string('category')));
        }

        if ($request->string('sort') === 'price_asc') {
            $query->orderByRaw('COALESCE(sale_price, price) asc');
        } elseif ($request->string('sort') === 'price_desc') {
            $query->orderByRaw('COALESCE(sale_price, price) desc');
        } elseif ($request->string('sort') === 'popular') {
            $query->orderByDesc('sales_count');
        } else {
            $query->latest();
        }

        $themes = $query->paginate(12)->withQueryString();
        $categories = Category::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('themes.index', compact('themes', 'categories'));
    }

    public function show(string $slug): View
    {
        $theme = Theme::query()
            ->with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Theme::query()
            ->with('category')
            ->where('is_active', true)
            ->where('category_id', $theme->category_id)
            ->where('id', '!=', $theme->id)
            ->take(4)
            ->get();

        return view('themes.show', compact('theme', 'related'));
    }
}
