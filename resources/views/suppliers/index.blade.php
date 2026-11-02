@extends('layouts.app')
@section('title', 'Suppliers')
@section('section', 'Suppliers')
@section('content')
<div class="page-heading"><div><div class="eyebrow">Partner records</div><h1>Suppliers</h1><div class="muted">Maintain supplier contacts used for purchasing and receiving stock.</div></div><div class="toolbar"><a class="btn" href="{{ route('suppliers.archived') }}">Archived</a><a class="btn primary" href="{{ route('suppliers.create') }}">Add supplier</a></div></div>
@if($errors->has('supplier'))<div class="flash" style="background:#fce8e7;color:#a6423c">{{ $errors->first('supplier') }}</div>@endif
<section class="panel"><div class="panel-head"><h2>Supplier directory <span class="muted">({{ $suppliers->total() }})</span></h2></div><div class="table-wrap"><table><thead><tr><th>Name</th><th>Contact Person</th><th>Phone</th><th>Email</th><th>Address</th><th>City</th><th>Country</th><th>Actions</th></tr></thead><tbody>
@forelse($suppliers as $supplier)<tr><td><strong>{{ $supplier->company_name }}</strong></td><td>{{ $supplier->contact_person ?: '—' }}</td><td>{{ $supplier->phone ?: '—' }}</td><td>{{ $supplier->email ?: '—' }}</td><td>{{ $supplier->address ?: '—' }}</td><td>{{ $supplier->city ?: '—' }}</td><td>{{ $supplier->country ?: '—' }}</td><td><a class="btn" href="{{ route('suppliers.edit', $supplier) }}">Edit</a> <form style="display:inline" method="post" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Archive this supplier? Its history will remain available.')">@csrf @method('DELETE')<button class="btn danger" type="submit">Archive</button></form></td></tr>@empty<tr><td colspan="8" style="padding:26px;text-align:center;color:#71848b">No suppliers yet.</td></tr>@endforelse
</tbody></table></div><div class="pagination"><span>{{ $suppliers->total() }} suppliers</span>{{ $suppliers->links('pagination::simple-tailwind') }}</div></section>
@endsection
