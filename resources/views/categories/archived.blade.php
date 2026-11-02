@extends('layouts.app')
@section('title', 'Archived categories')
@section('section', 'Categories')
@section('content')
<style>
    .archived-category-actions{display:flex;align-items:center;gap:7px;white-space:nowrap}
    .archived-category-actions .btn:disabled{opacity:.5;cursor:not-allowed}
</style>
<div class="page-heading"><div><div class="eyebrow">Product master data</div><h1>Archived categories</h1><div class="muted">Restore a category to make it selectable for new products.</div></div><a class="btn" href="{{ route('categories.index') }}">Active categories</a></div>
@if($errors->has('category_delete'))<div class="flash" style="background:#fce8e7;color:#a6423c">{{ $errors->first('category_delete') }}</div>@endif
<section class="panel"><div class="panel-head"><h2>Archived categories</h2></div><div class="table-wrap"><table><thead><tr><th>Name</th><th>Description</th><th>Linked products</th><th>Archived</th><th>Actions</th></tr></thead><tbody>
@forelse($categories as $category)
    <tr><td>{{ $category->name }}</td><td>{{ $category->description ?: '-' }}</td><td>{{ $category->products_count }}</td><td>{{ $category->deleted_at->format('M d, Y') }}</td><td><div class="archived-category-actions">
        <form method="post" action="{{ route('categories.restore', $category->id) }}">@csrf<button class="btn primary" type="submit">Restore</button></form>
        @if((int) $category->products_count === 0)
            <form method="post" action="{{ route('categories.permanently-delete', $category->id) }}" onsubmit="return confirm('Permanently delete this category? This cannot be undone.')">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete</button></form>
        @else
            <button class="btn danger" type="button" disabled title="Delete all linked products first">Delete</button>
        @endif
    </div></td></tr>
@empty<tr><td colspan="5" style="padding:24px;text-align:center;color:#71848b">No archived categories.</td></tr>@endforelse
</tbody></table></div><div class="pagination"><span>{{ $categories->total() }} archived categories</span>{{ $categories->links('pagination::simple-tailwind') }}</div></section>
@endsection
