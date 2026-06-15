<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Theme;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['themes' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(string $slug): View
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $themes = Theme::query()
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return view('categories.show', compact('category', 'themes'));
    }
}
