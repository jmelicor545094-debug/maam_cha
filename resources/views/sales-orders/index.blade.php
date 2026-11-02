@extends('layouts.app')
@section('title', 'Sales orders')
@section('section', 'Sales orders')
@section('content')
<div class="page-heading"><div><div class="eyebrow">Order fulfillment</div><h1>Sales orders</h1><div class="muted">Draft, confirm, dispense and track customer orders.</div></div><a class="btn primary" href="{{ route('sales-orders.create') }}">＋ Create order</a></div>
<section class="panel"><div class="panel-head"><h2>Order register <span class="muted">({{ $orders->total() }})</span></h2></div><div class="table-wrap"><table><thead><tr><th>Order no.</th><th>Customer</th><th>Delivery</th><th>Items</th><th>Status</th><th>Created</th><th></th></tr></thead><tbody>
@forelse($orders as $order)<tr><td><strong>{{ $order->order_number }}</strong></td><td>{{ $order->customer_name }}</td><td>{{ ucfirst($order->delivery_method) }}</td><td>{{ $order->items_count }}</td><td><span class="badge {{ $order->status === 'dispensed' ? 'green' : ($order->status === 'cancelled' ? 'red' : '') }}">{{ ucfirst($order->status) }}</span></td><td>{{ $order->created_at->format('M d, Y') }}</td><td><a class="btn" href="{{ route('sales-orders.show', $order) }}">Open</a></td></tr>@empty<tr><td colspan="7" style="padding:26px;text-align:center;color:#71848b">No sales orders yet.</td></tr>@endforelse
</tbody></table></div><div class="pagination"><span>{{ $orders->total() }} orders</span>{{ $orders->links('pagination::simple-tailwind') }}</div></section>
@endsection
