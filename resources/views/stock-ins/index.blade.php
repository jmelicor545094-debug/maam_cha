@extends('layouts.app')
@section('title', 'Stock-in')
@section('section', 'Stock-in')
@section('content')
<div class="page-heading"><div><div class="eyebrow">Inventory receiving</div><h1>Stock-in transactions</h1><div class="muted">Encode supplier deliveries, then approve to update batch inventory.</div></div><a class="btn primary" href="{{ route('stock-ins.create') }}">＋ Receive stock</a></div>
@if($errors->has('stock_in'))<div class="flash" style="background:#fce8e7;color:#a6423c">{{ $errors->first('stock_in') }}</div>@endif
<section class="panel"><div class="panel-head"><h2>Receiving history <span class="muted">({{ $stockIns->total() }})</span></h2></div><div class="table-wrap"><table><thead><tr><th>Reference</th><th>Supplier</th><th>Date received</th><th>Lines</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($stockIns as $entry)<tr><td><strong>{{ $entry->reference_number }}</strong></td><td>{{ $entry->supplier->company_name }}</td><td>{{ $entry->received_at->format('M d, Y') }}</td><td>{{ $entry->items_count }}</td><td><span class="badge {{ $entry->status === 'approved' ? 'green' : '' }}">{{ ucfirst($entry->status) }}</span></td><td>@if($entry->status === 'pending')<form method="post" action="{{ route('stock-ins.approve', $entry) }}">@csrf<button class="btn primary">Approve receipt</button></form>@else<span class="muted">Inventory updated</span>@endif</td></tr>@empty<tr><td colspan="6" style="padding:26px;text-align:center;color:#71848b">No stock-in records yet. Receive a supplier delivery to begin.</td></tr>@endforelse
</tbody></table></div><div class="pagination"><span>{{ $stockIns->total() }} transactions</span>{{ $stockIns->links('pagination::simple-tailwind') }}</div></section>
@endsection
