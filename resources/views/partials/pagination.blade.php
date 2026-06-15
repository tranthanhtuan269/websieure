@if ($paginator->hasPages())
    <nav style="display:flex;gap:.5rem;flex-wrap:wrap;">
        @if ($paginator->onFirstPage())
            <span class="btn btn-outline btn-sm" style="opacity:.5;">←</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-outline btn-sm">←</a>
        @endif
        <span style="padding:.4rem .75rem;color:var(--muted);">Trang {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-outline btn-sm">→</a>
        @else
            <span class="btn btn-outline btn-sm" style="opacity:.5;">→</span>
        @endif
    </nav>
@endif
