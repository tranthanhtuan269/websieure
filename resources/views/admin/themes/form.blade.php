@extends('layouts.admin')

@section('title', $theme->exists ? 'Sửa theme' : 'Thêm theme')

@section('content')
<h1 style="margin-bottom:1rem;">{{ $theme->exists ? 'Sửa theme' : 'Thêm theme' }}</h1>
<form method="POST" action="{{ $theme->exists ? route('admin.themes.update', $theme) : route('admin.themes.store') }}" class="form-card">
    @csrf
    @if($theme->exists) @method('PUT') @endif
    <div class="form-group">
        <label>Chủ đề *</label>
        <select name="category_id" required>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $theme->category_id) == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group"><label>Tên *</label><input name="name" value="{{ old('name', $theme->name) }}" required></div>
    <div class="form-group"><label>Slug</label><input name="slug" value="{{ old('slug', $theme->slug) }}"></div>
    <div class="form-group"><label>Tagline</label><input name="tagline" value="{{ old('tagline', $theme->tagline) }}"></div>
    <div class="form-group"><label>Mô tả</label><textarea name="description" rows="4">{{ old('description', $theme->description) }}</textarea></div>
    <div class="form-group"><label>Tính năng (mỗi dòng 1 mục)</label><textarea name="features" rows="5">{{ old('features', is_array($theme->features) ? implode("\n", $theme->features) : '') }}</textarea></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="form-group"><label>Giá (VNĐ) *</label><input type="number" name="price" value="{{ old('price', $theme->price) }}" required></div>
        <div class="form-group"><label>Giá sale</label><input type="number" name="sale_price" value="{{ old('sale_price', $theme->sale_price) }}"></div>
    </div>
    <div class="form-group"><label>URL demo</label><input name="preview_url" value="{{ old('preview_url', $theme->preview_url) }}"></div>
    <div class="form-group"><label>Ảnh thumbnail (path trong storage/public, vd: themes/thuoc360-home.png)</label><input name="thumbnail_image" value="{{ old('thumbnail_image', $theme->thumbnail_image) }}"></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="form-group"><label>Màu thumbnail</label><input type="color" name="thumbnail_color" value="{{ old('thumbnail_color', $theme->thumbnail_color) }}"></div>
        <div class="form-group"><label>Nhãn thumbnail</label><input name="thumbnail_label" value="{{ old('thumbnail_label', $theme->thumbnail_label) }}"></div>
    </div>
    <label style="margin-right:1rem;"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $theme->is_featured))> Nổi bật</label>
    <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $theme->is_active))> Hiển thị</label>
    <div style="margin-top:1rem;"><button class="btn btn-primary">Lưu</button></div>
</form>
@endsection
