<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <strong>{{ config('site.name') }}</strong>
            <p>{{ config('site.tagline') }}</p>
            <p style="margin-top:.75rem;">Theme website chuyên nghiệp theo từng ngành — cài đặt nhanh, hỗ trợ tận tình.</p>
        </div>
        <div>
            <strong>Liên kết</strong>
            <p><a href="{{ route('themes.index') }}">Kho theme</a></p>
            <p><a href="{{ route('categories.index') }}">Chủ đề</a></p>
        </div>
        <div>
            <strong>Liên hệ</strong>
            <p>{{ config('site.contact_email') }}</p>
            <p>{{ config('site.contact_phone') }}</p>
        </div>
    </div>
</footer>
