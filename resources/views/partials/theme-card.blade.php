<article class="theme-card">
    <div class="theme-thumb @if($theme->thumbnailUrl()) theme-thumb--image @endif"
         @unless($theme->thumbnailUrl()) style="background: linear-gradient(135deg, {{ $theme->thumbnail_color }}, {{ $theme->category?->color ?? '#0891b2' }});" @endunless>
        @if($theme->thumbnailUrl())
            <img src="{{ $theme->thumbnailUrl() }}" alt="Ảnh demo {{ $theme->name }}" loading="lazy">
        @elseif($theme->thumbnail_label)
            {{ $theme->thumbnail_label }}
        @else
            {{ $theme->name }}
        @endif
        <small>{{ $theme->category?->name }}</small>
        @if($theme->isOnSale())
            <span class="badge-sale">-{{ $theme->discountPercent() }}%</span>
        @endif
    </div>
    <div class="theme-body">
        <h3><a href="{{ route('themes.show', $theme) }}">{{ $theme->name }}</a></h3>
        <p class="theme-tagline">{{ $theme->tagline }}</p>
        <div class="theme-meta">
            <div class="price">
                @if($theme->isOnSale())
                    <span class="price-old">{{ number_format($theme->price, 0, ',', '.') }} ₫</span>
                @endif
                {{ $theme->formattedPrice() }}
            </div>
            <a href="{{ route('themes.show', $theme) }}" class="btn btn-outline btn-sm">Xem chi tiết</a>
        </div>
    </div>
</article>
