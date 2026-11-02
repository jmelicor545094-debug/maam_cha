@extends('layouts.app')
@section('title', 'Archived suppliers')
@section('section', 'Suppliers')
@section('content')
<div class="page-heading"><div><div class="eyebrow">Partner records</div><h1>Archived suppliers</h1><div class="muted">Restore a supplier to make it available for purchase orders and receiving.</div></div><a class="btn" href="{{ route('suppliers.index') }}">Active suppliers</a></div>
<section class="panel"><div class="panel-head"><h2>Archived suppliers</h2></div><div class="table-wrap"><table><thead><tr><th>Company</th><th>Contact</th><th>Email</th><th>Archived</th><th></th></tr></thead><tbody>@forelse($suppliers as $supplier)<tr><td>{{ $supplier->company_name }}</td><td>{{ $supplier->contact_person }}</td><td>{{ $supplier->email }}</td><td>{{ $supplier->deleted_at->format('M d, Y') }}</td><td><form method="post" action="{{ route('suppliers.restore',$supplier->id) }}">@csrf<button class="btn primary">Restore</button></form></td></tr>@empty<tr><td colspan="5" style="padding:24px;text-align:center;color:#71848b">No archived suppliers.</td></tr>@endforelse</tbody></table></div><div class="pagination"><span>{{ $suppliers->total() }} archived suppliers</span>{{ $suppliers->links('pagination::simple-tailwind') }}</div></section>
@endsection
