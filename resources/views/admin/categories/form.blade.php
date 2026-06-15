@extends('layouts.admin')

@section('title', $category->exists ? 'Sửa chủ đề' : 'Thêm chủ đề')

@section('content')
<h1 style="margin-bottom:1rem;">{{ $category->exists ? 'Sửa chủ đề' : 'Thêm chủ đề' }}</h1>
<form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="form-card" style="max-width:640px;">
    @csrf
    @if($category->exists) @method('PUT') @endif
    <div class="form-group"><label>Tên *</label><input name="name" value="{{ old('name', $category->name) }}" required></div>
    <div class="form-group"><label>Slug</label><input name="slug" value="{{ old('slug', $category->slug) }}"></div>
    <div class="form-group"><label>Icon (emoji)</label><input name="icon" value="{{ old('icon', $category->icon) }}"></div>
    <div class="form-group"><label>Màu</label><input type="color" name="color" value="{{ old('color', $category->color) }}"></div>
    <div class="form-group"><label>Mô tả</label><textarea name="description" rows="3">{{ old('description', $category->description) }}</textarea></div>
    <div class="form-group"><label>Thứ tự</label><input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}"></div>
    <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))> Hiển thị</label>
    <div style="margin-top:1rem;"><button class="btn btn-primary">Lưu</button></div>
</form>
@endsection
