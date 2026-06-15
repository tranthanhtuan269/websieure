<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ThemeController extends Controller
{
    public function index(): View
    {
        $themes = Theme::with('category')->latest()->paginate(20);

        return view('admin.themes.index', compact('themes'));
    }

    public function create(): View
    {
        return view('admin.themes.form', [
            'theme' => new Theme(['is_active' => true, 'price' => 990000, 'thumbnail_color' => '#4f46e5']),
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Theme::create($this->validated($request));

        return redirect()->route('admin.themes.index')->with('success', 'Đã thêm theme.');
    }

    public function edit(Theme $theme): View
    {
        return view('admin.themes.form', [
            'theme' => $theme,
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Theme $theme): RedirectResponse
    {
        $theme->update($this->validated($request, $theme));

        return redirect()->route('admin.themes.index')->with('success', 'Đã cập nhật theme.');
    }

    public function destroy(Theme $theme): RedirectResponse
    {
        $theme->delete();

        return redirect()->route('admin.themes.index')->with('success', 'Đã xóa theme.');
    }

    private function validated(Request $request, ?Theme $theme = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'features' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'sale_price' => ['nullable', 'integer', 'min:0'],
            'preview_url' => ['nullable', 'url', 'max:500'],
            'thumbnail_image' => ['nullable', 'string', 'max:500'],
            'thumbnail_color' => ['required', 'string', 'max:7'],
            'thumbnail_label' => ['nullable', 'string', 'max:32'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $data['slug'] = filled($data['slug'] ?? null) ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        $data['features'] = filled($data['features'] ?? null)
            ? array_values(array_filter(array_map('trim', explode("\n", $data['features']))))
            : null;
        $data['sale_price'] = filled($data['sale_price'] ?? null) ? (int) $data['sale_price'] : null;

        return $data;
    }
}
