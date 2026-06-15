@extends('layouts.admin')

@section('title', 'Chủ đề')

@section('content')
<div style="display:flex;justify-content:space-between;margin-bottom:1rem;">
    <h1>Chủ đề</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ Thêm</a>
</div>
<table class="admin-table">
    <thead><tr><th>Tên</th><th>Slug</th><th>Theme</th><th>TT</th><th></th></tr></thead>
    <tbody>
        @foreach($categories as $category)
        <tr>
            <td>{{ $category->icon }} {{ $category->name }}</td>
            <td><code>{{ $category->slug }}</code></td>
            <td>{{ $category->themes_count }}</td>
            <td>{{ $category->is_active ? 'Hiện' : 'Ẩn' }}</td>
            <td>
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline btn-sm">Sửa</a>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa chủ đề?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline btn-sm">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $categories->links() }}
@endsection
