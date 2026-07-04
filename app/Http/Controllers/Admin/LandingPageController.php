<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LandingPageType;
use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Services\LandingPageExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LandingPageController extends Controller
{
    public function index(LandingPageExportService $exporter): View
    {
        $pages = LandingPage::query()
            ->orderBy('type')
            ->orderBy('title')
            ->paginate(30);

        $exportZip = $exporter->exportZipPath();
        $exportReady = File::exists($exportZip);

        return view('admin.landing-pages.index', compact('pages', 'exportReady', 'exportZip'));
    }

    public function create(): View
    {
        return view('admin.landing-pages.form', [
            'page' => new LandingPage(['is_active' => true, 'type' => LandingPageType::Standard]),
            'types' => LandingPageType::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        LandingPage::create($this->validated($request));

        return redirect()->route('admin.landing-pages.index')->with('success', 'Đã thêm landing page.');
    }

    public function edit(LandingPage $landingPage): View
    {
        return view('admin.landing-pages.form', [
            'page' => $landingPage,
            'types' => LandingPageType::cases(),
        ]);
    }

    public function update(Request $request, LandingPage $landingPage): RedirectResponse
    {
        $landingPage->update($this->validated($request, $landingPage));

        return redirect()->route('admin.landing-pages.index')->with('success', 'Đã cập nhật landing page.');
    }

    public function destroy(LandingPage $landingPage): RedirectResponse
    {
        $landingPage->delete();

        return redirect()->route('admin.landing-pages.index')->with('success', 'Đã xóa landing page.');
    }

    public function exportPackage(LandingPageExportService $exporter): RedirectResponse
    {
        $result = $exporter->export();

        return redirect()
            ->route('admin.landing-pages.index')
            ->with('success', "Đã tạo gói {$result['count']} landing page HTML. Bạn có thể tải file zip ngay.");
    }

    public function downloadExport(LandingPageExportService $exporter): BinaryFileResponse|RedirectResponse
    {
        $zipPath = $exporter->exportZipPath();

        if (! File::exists($zipPath)) {
            return redirect()
                ->route('admin.landing-pages.index')
                ->with('error', 'Chưa có file zip. Hãy bấm "Tạo gói export" trước.');
        }

        return response()->download($zipPath, 'lamwebre-landing-pages.zip');
    }

    private function validated(Request $request, ?LandingPage $page = null): array
    {
        $data = $request->validate([
            'type' => ['required', 'in:standard,popup,scroll'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'hero_image' => ['nullable', 'string', 'max:500'],
            'affiliate_url' => ['nullable', 'url', 'max:500'],
            'intro' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'sections_json' => ['nullable', 'string'],
            'popup_title' => ['nullable', 'string', 'max:255'],
            'popup_message' => ['nullable', 'string'],
            'popup_button_text' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        $data['slug'] = filled($data['slug'] ?? null)
            ? Str::slug($data['slug'])
            : Str::slug($data['title']);
        $data['is_active'] = $request->boolean('is_active');
        $data['type'] = LandingPageType::from($data['type']);

        $data['sections'] = filled($data['sections_json'] ?? null)
            ? json_decode($data['sections_json'], true)
            : null;
        unset($data['sections_json']);

        if ($data['type'] === LandingPageType::Popup) {
            $data['popup_settings'] = [
                'title' => $data['popup_title'] ?? 'Cookie Settings',
                'message' => $data['popup_message'] ?? '',
                'button_text' => $data['popup_button_text'] ?? 'Yes, I accept',
            ];
        }

        unset($data['popup_title'], $data['popup_message'], $data['popup_button_text']);

        return $data;
    }
}
