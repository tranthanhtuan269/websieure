@extends('layouts.admin')

@section('title', 'Themes')

@section('content')
<div style="display:flex;justify-content:space-between;margin-bottom:1rem;">
    <h1>Themes</h1>
    <a href="{{ route('admin.themes.create') }}" class="btn btn-primary">+ Thêm theme</a>
</div>
<table class="admin-table">
    <thead><tr><th>Tên</th><th>Chủ đề</th><th>Giá</th><th>Nổi bật</th><th></th></tr></thead>
    <tbody>
        @foreach($themes as $theme)
        <tr>
            <td>{{ $theme->name }}</td>
            <td>{{ $theme->category?->name }}</td>
            <td>{{ $theme->formattedPrice() }}</td>
            <td>{{ $theme->is_featured ? '★' : '—' }}</td>
            <td>
                <a href="{{ route('admin.themes.edit', $theme) }}" class="btn btn-outline btn-sm">Sửa</a>
                <form action="{{ route('admin.themes.destroy', $theme) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa theme?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline btn-sm">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $themes->links() }}
@endsection
