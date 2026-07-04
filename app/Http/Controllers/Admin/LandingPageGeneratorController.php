<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingPageGeneration;
use App\Services\LandingAiService;
use App\Services\LandingPagePackExportService;
use App\Services\LandingPagePackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LandingPageGeneratorController extends Controller
{
    public function __construct(
        private LandingPagePackService $packService,
        private LandingPagePackExportService $exportService,
        private LandingAiService $aiService,
    ) {}

    public function create(): View
    {
        return view('admin.landing-pages.generator', [
            'aiConfigured' => $this->aiService->isConfigured(),
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        $data = $request->validate([
            'affiliate_url' => ['required', 'url', 'max:500'],
        ]);

        $generation = LandingPageGeneration::create([
            'user_id' => Auth::id(),
            'affiliate_url' => $data['affiliate_url'],
            'status' => 'pending',
            'step' => 'pending',
            'progress' => 0,
        ]);

        return response()->json([
            'id' => $generation->id,
            'steps' => ['crawl', 'standard', 'compare', 'popup', 'zip'],
        ]);
    }

    public function runStep(LandingPageGeneration $generation, string $step): JsonResponse
    {
        $allowed = ['crawl', 'standard', 'compare', 'popup', 'zip'];
        if (! in_array($step, $allowed, true)) {
            return response()->json(['error' => 'Bước không hợp lệ.'], 422);
        }

        $generation = $this->packService->runStep($generation, $step);

        return response()->json($this->statusPayload($generation));
    }

    public function status(LandingPageGeneration $generation): JsonResponse
    {
        return response()->json($this->statusPayload($generation));
    }

    public function download(LandingPageGeneration $generation): BinaryFileResponse|RedirectResponse
    {
        if (! $generation->isCompleted() || ! $generation->zip_path || ! File::exists($generation->zip_path)) {
            return redirect()
                ->route('admin.landing-pages.generator')
                ->with('error', 'Gói chưa sẵn sàng để tải.');
        }

        return response()->download(
            $generation->zip_path,
            'lamwebre-landing-pack-'.$generation->id.'.zip'
        );
    }

    public function preview(LandingPageGeneration $generation, string $type): View|RedirectResponse
    {
        $preview = $this->exportService->buildPreviewViewData($generation, $type);

        if ($preview === null) {
            return redirect()
                ->route('admin.landing-pages.generator')
                ->with('error', 'Không tìm thấy landing page để xem trước.');
        }

        return view($preview['view'], $preview['data']);
    }

    public function previewImage(LandingPageGeneration $generation, string $filename): BinaryFileResponse|RedirectResponse
    {
        $path = $this->exportService->previewImagePath($generation, $filename);

        if ($path === null) {
            abort(404);
        }

        return response()->file($path);
    }

    /**
     * @return array<string, mixed>
     */
    private function statusPayload(LandingPageGeneration $generation): array
    {
        $labels = [
            'pending' => 'Chuẩn bị...',
            'crawl' => 'Đang crawl ảnh từ link affiliate...',
            'standard' => 'AI tạo landing chuẩn (~3000 từ, 3 ảnh)...',
            'compare' => 'AI tạo bảng so sánh đối thủ...',
            'popup' => 'Đang tạo landing popup Cookie Notice...',
            'zip' => 'Đang đóng gói file zip...',
            'done' => 'Hoàn tất!',
        ];

        return [
            'id' => $generation->id,
            'status' => $generation->status,
            'step' => $generation->step,
            'progress' => $generation->progress,
            'message' => $labels[$generation->step] ?? $generation->step,
            'error' => $generation->error_message,
            'download_url' => $generation->isCompleted()
                ? route('admin.landing-pages.generator.download', $generation)
                : null,
            'preview_links' => $generation->isCompleted()
                ? $this->exportService->previewLinks($generation)
                : [],
            'topic' => $generation->topic,
        ];
    }
}
