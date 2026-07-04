@extends('layouts.admin')

@section('title', $page->exists ? 'Sửa landing page' : 'Thêm landing page')

@section('content')
<h1 style="margin-bottom:1rem;">{{ $page->exists ? 'Sửa landing page' : 'Thêm landing page' }}</h1>
<form method="POST" action="{{ $page->exists ? route('admin.landing-pages.update', $page) : route('admin.landing-pages.store') }}" class="form-card">
    @csrf
    @if($page->exists) @method('PUT') @endif

    <div class="form-group">
        <label>Loại *</label>
        <select name="type" required>
            @foreach($types as $type)
                <option value="{{ $type->value }}" @selected(old('type', $page->type?->value) === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group"><label>Tiêu đề *</label><input name="title" value="{{ old('title', $page->title) }}" required></div>
    <div class="form-group"><label>Slug (URL: /lp/slug)</label><input name="slug" value="{{ old('slug', $page->slug) }}"></div>
    <div class="form-group"><label>Meta description</label><textarea name="meta_description" rows="2">{{ old('meta_description', $page->meta_description) }}</textarea></div>
    <div class="form-group"><label>Ảnh hero (URL)</label><input name="hero_image" value="{{ old('hero_image', $page->hero_image) }}"></div>
    <div class="form-group"><label>Link affiliate (để trống = dùng mặc định)</label><input name="affiliate_url" value="{{ old('affiliate_url', $page->affiliate_url) }}"></div>

    <div class="form-group"><label>Intro (landing chuẩn)</label><textarea name="intro" rows="4">{{ old('intro', $page->intro) }}</textarea></div>
    <div class="form-group"><label>Nội dung (landing cuộn)</label><textarea name="body" rows="8">{{ old('body', $page->body) }}</textarea></div>
    <div class="form-group">
        <label>Sections JSON (landing chuẩn — 3 phần)</label>
        <textarea name="sections_json" rows="10">{{ old('sections_json', $page->sections ? json_encode($page->sections, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
    </div>

    @php $popup = $page->popup_settings ?? []; @endphp
    <fieldset style="border:1px solid var(--border);border-radius:10px;padding:1rem;margin:1rem 0;">
        <legend>Popup settings</legend>
        <div class="form-group"><label>Tiêu đề popup</label><input name="popup_title" value="{{ old('popup_title', $popup['title'] ?? 'Cookie Settings') }}"></div>
        <div class="form-group"><label>Nội dung popup</label><textarea name="popup_message" rows="3">{{ old('popup_message', $popup['message'] ?? '') }}</textarea></div>
        <div class="form-group"><label>Nút chấp nhận</label><input name="popup_button_text" value="{{ old('popup_button_text', $popup['button_text'] ?? 'Yes, I accept') }}"></div>
    </fieldset>

    <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $page->is_active ?? true))> Hiển thị</label>
    <div style="margin-top:1rem;"><button class="btn btn-primary">Lưu</button></div>
</form>
@endsection
