@extends('layouts.admin')

@section('title', 'Landing pages')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem;">
    <h1>Landing pages</h1>
    <a href="{{ route('admin.landing-pages.create') }}" class="btn btn-primary btn-sm">+ Thêm landing page</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Tiêu đề</th>
            <th>Loại</th>
            <th>Slug</th>
            <th>Số từ</th>
            <th>Trạng thái</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($pages as $page)
        <tr>
            <td>{{ $page->title }}</td>
            <td>{{ $page->typeLabel() }}</td>
            <td><a href="{{ route('landing-pages.show', $page) }}" target="_blank">/lp/{{ $page->slug }}</a></td>
            <td>{{ number_format($page->wordCount()) }}</td>
            <td>{{ $page->is_active ? 'Hiển thị' : 'Ẩn' }}</td>
            <td style="white-space:nowrap;">
                <a href="{{ route('admin.landing-pages.edit', $page) }}" class="btn btn-outline btn-sm">Sửa</a>
                <form action="{{ route('admin.landing-pages.destroy', $page) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa landing page này?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline btn-sm">Xóa</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6">Chưa có landing page. Chạy <code>php artisan db:seed --class=LandingPageSeeder</code></td></tr>
        @endforelse
    </tbody>
</table>
{{ $pages->links() }}
@endsection
