@extends('layouts.app')
@section('title', 'Archived products')
@section('section', 'Products')
@section('content')
<div class="page-heading"><div><div class="eyebrow">Inventory master data</div><h1>Archived products</h1><div class="muted">Restore a product to return it to active inventory records.</div></div><a class="btn" href="{{ route('products.index') }}">Active products</a></div>
<section class="panel"><div class="panel-head"><h2>Archived catalog</h2></div><div class="table-wrap"><table><thead><tr><th>SKU</th><th>Name</th><th>Category</th><th>Archived</th><th></th></tr></thead><tbody>@forelse($products as $product)<tr><td>{{ $product->sku }}</td><td>{{ $product->name }}</td><td>{{ $product->categoryRecord?->name ?? $product->category }}</td><td>{{ $product->deleted_at->format('M d, Y') }}</td><td><form method="post" action="{{ route('products.restore',$product->id) }}">@csrf<button class="btn primary">Restore</button></form></td></tr>@empty<tr><td colspan="5" style="padding:24px;text-align:center;color:#71848b">No archived products.</td></tr>@endforelse</tbody></table></div><div class="pagination"><span>{{ $products->total() }} archived products</span>{{ $products->links('pagination::simple-tailwind') }}</div></section>
@endsection
