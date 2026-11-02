@extends('layouts.app')
@section('title', 'Categories')
@section('section', 'Categories')
@section('content')
<style>
    .category-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:18px}
    .category-heading h1{font-size:23px;margin:0 0 5px;color:#243a42}
    .category-crumb{font-size:11px;color:#819196}
    .category-add{position:relative}
    .category-add summary{list-style:none;cursor:pointer}
    .category-add summary::-webkit-details-marker,.category-edit summary::-webkit-details-marker{display:none}
    .category-add-popover{position:absolute;right:0;top:42px;width:min(360px,calc(100vw - 32px));z-index:10;padding:16px;background:#fff;border:1px solid #dfe8e7;border-radius:6px;box-shadow:0 12px 32px #183b4024}
    .category-add-popover h2{font-size:14px;margin:0 0 12px}
    .category-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:12px 16px;border-bottom:1px solid #e9eeee}
    .category-length,.category-search{display:flex;align-items:center;gap:8px;font-size:11px;color:#657980}
    .category-length select{width:auto;min-width:66px;padding:7px 8px}
    .category-search input{width:190px;padding:8px 10px}
    .category-search .btn{min-height:34px;padding:7px 10px}
    .category-table th,.category-table td{padding:12px 16px}
    .category-table th{background:#f7f9f9}
    .category-name{font-weight:600;color:#31494f}
    .category-description{font-size:10px;color:#829399;margin-top:3px;white-space:normal;max-width:440px}
    .category-actions{display:flex;justify-content:flex-end;gap:7px}
    .category-edit{position:relative}
    .category-edit summary{list-style:none;cursor:pointer}
    .category-edit .icon-btn{width:32px;height:32px;padding:0;border:0;border-radius:4px;background:#16796b;color:white}
    .category-edit-popover{position:absolute;right:0;top:38px;z-index:9;width:min(310px,calc(100vw - 40px));padding:14px;background:#fff;border:1px solid #dfe8e7;border-radius:6px;box-shadow:0 12px 30px #183b4024;white-space:normal}
    .category-edit-popover .field{margin:0 0 10px}
    .category-edit-popover input{font-size:12px}
    .category-footer{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 16px;color:#71848b;font-size:11px}
    .category-footer nav{display:flex;align-items:center;gap:4px}
    .category-footer nav a,.category-footer nav span{padding:6px 9px;border:1px solid #e0e7e8;border-radius:3px}
    .category-footer nav [aria-current=page]{background:#16796b;color:#fff;border-color:#16796b}
    .category-footer nav .disabled{color:#a5b0b3;background:#f7f9f9}
    @media(max-width:600px){.category-heading{align-items:center}.category-heading h1{font-size:21px}.category-toolbar{align-items:stretch;flex-direction:column}.category-search{width:100%}.category-search input{width:100%}.category-footer{align-items:flex-start;flex-direction:column}.category-actions{justify-content:flex-start}}
</style>
<div class="category-heading">
    <div><h1>Categories</h1><div class="category-crumb">Dashboard / Categories</div></div>
    @if(auth()->user()->role === 'admin')
        <div class="toolbar">
            <details class="category-add"><summary class="btn primary">+ Add Category</summary>
                <form class="category-add-popover" method="post" action="{{ route('categories.store') }}">
                    @csrf<h2>New category</h2>
                    <div class="field"><label for="category-name">Name</label><input id="category-name" name="name" value="{{ old('name') }}" required maxlength="100"></div>
                    <div class="field"><label for="category-description">Description</label><input id="category-description" name="description" value="{{ old('description') }}" maxlength="1000"></div>
                    <button class="btn primary" type="submit">Save category</button>
                </form>
            </details>
        </div>
    @endif
</div>
@if($errors->any())<div class="flash" style="background:#fce8e7;color:#a6423c;border-color:#f3d3d0">{{ $errors->first() }}</div>@endif
<section class="panel">
    <div class="panel-head"><h2>Category register <span class="muted">({{ $categories->total() }})</span></h2></div>
    <form class="category-toolbar" method="get" action="{{ route('categories.index') }}">
        <label class="category-length">Show <select name="per_page" onchange="this.form.submit()"><option value="10" @selected($perPage === 10)>10</option><option value="25" @selected($perPage === 25)>25</option><option value="50" @selected($perPage === 50)>50</option></select> entries</label>
        <label class="category-search"><span>Search:</span><input name="search" value="{{ request('search') }}" aria-label="Search categories" placeholder="Category name"><button class="btn" type="submit">Search</button></label>
    </form>
    <div class="table-wrap"><table class="category-table"><thead><tr><th>Name</th><th>Created date</th><th style="text-align:right">Actions</th></tr></thead><tbody>
        @forelse($categories as $category)
            <tr>
                <td><div class="category-name">{{ $category->name }}</div>@if($category->description)<div class="category-description">{{ $category->description }}</div>@endif</td>
                <td>{{ $category->created_at?->format('d M, Y') }}</td>
                <td><div class="category-actions">
                    @if(auth()->user()->role === 'admin')
                        <details class="category-edit"><summary class="btn icon-btn" aria-label="Edit {{ $category->name }}" title="Edit category">&#9998;</summary>
                            <form class="category-edit-popover" method="post" action="{{ route('categories.update', $category) }}">@csrf @method('PUT')
                                <div class="field"><label>Name</label><input name="name" value="{{ $category->name }}" required maxlength="100"></div>
                                <div class="field"><label>Description</label><input name="description" value="{{ $category->description }}" maxlength="1000"></div>
                                <button class="btn primary" type="submit">Save changes</button>
                            </form>
                        </details>
                        <form method="post" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Permanently delete this category? This cannot be undone.')">@csrf @method('DELETE')<button class="btn danger" type="submit" @disabled($category->products_count > 0) title="{{ $category->products_count > 0 ? 'Remove or reassign linked products before deleting this category.' : 'Delete category permanently' }}">Delete</button></form>
                    @else
                        <span class="muted">View only</span>
                    @endif
                </div></td>
            </tr>
        @empty
            <tr><td colspan="3" style="padding:28px;text-align:center;color:#71848b">No categories found.</td></tr>
        @endforelse
    </tbody></table></div>
    <div class="category-footer"><span>Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} entries</span>
        @php($firstPage = max(1, $categories->currentPage() - 2))
        @php($lastPage = min($categories->lastPage(), $categories->currentPage() + 2))
        <nav aria-label="Category pages">
            @if($categories->onFirstPage())<span class="disabled">Previous</span>@else<a href="{{ $categories->previousPageUrl() }}">Previous</a>@endif
            @if($firstPage > 1)<a href="{{ $categories->url(1) }}">1</a>@if($firstPage > 2)<span class="disabled">...</span>@endif @endif
            @for($page = $firstPage; $page <= $lastPage; $page++)@if($page === $categories->currentPage())<span aria-current="page">{{ $page }}</span>@else<a href="{{ $categories->url($page) }}">{{ $page }}</a>@endif @endfor
            @if($lastPage < $categories->lastPage())@if($lastPage < $categories->lastPage() - 1)<span class="disabled">...</span>@endif<a href="{{ $categories->url($categories->lastPage()) }}">{{ $categories->lastPage() }}</a>@endif
            @if($categories->hasMorePages())<a href="{{ $categories->nextPageUrl() }}">Next</a>@else<span class="disabled">Next</span>@endif
        </nav>
    </div>
</section>
@endsection
