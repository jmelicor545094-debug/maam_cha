@extends('layouts.app')
@section('title', match($type) { 'expired-items' => 'Expired Items', 'low-stock' => 'Low Stock', default => 'Expiring Soon' })
@section('section', 'Inventory Reports')
@section('content')
@php
    $title = match($type) { 'expired-items' => 'Expired Items', 'low-stock' => 'Low Stock', default => 'Expiring Soon' };
    $description = match($type) {
        'expired-items' => 'Batches past their expiry date that still have units recorded.',
        'low-stock' => 'Products at or below their minimum available quantity.',
        default => 'Active batches due to expire within the next 90 days.',
    };
@endphp
<style>
    .report-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px}
    .report-heading h1{font-size:23px;margin:0 0 5px;color:#243a42}
    .report-crumb{font-size:11px;color:#819196}
    .report-summary{font-size:12px;color:#71848b;margin-top:5px}
    .report-count{padding:5px 9px;background:#eaf3f1;color:#286f62;border-radius:4px;font-size:11px;font-weight:700}
    .report-table th,.report-table td{padding:12px 15px}
    @media(max-width:600px){.report-heading{flex-direction:column}.report-heading .toolbar{width:100%}}
</style>
<div class="report-heading">
    <div><h1>{{ $title }}</h1><div class="report-crumb">Dashboard / Inventory Reports / {{ $title }}</div><div class="report-summary">{{ $description }}</div></div>
    <div class="toolbar"><a class="btn" href="{{ route('reports.index') }}">All reports</a>@if($type === 'expired-items' && auth()->user()->role === 'admin')<a class="btn primary" href="{{ route('expired-stock.index') }}">Review cleanup</a>@endif</div>
</div>
<section class="panel">
    <div class="panel-head"><h2>{{ $title }} register</h2><span class="report-count">{{ $items->count() }} {{ $items->count() === 1 ? 'record' : 'records' }}</span></div>
    <div class="table-wrap"><table class="report-table">
        @if($type === 'low-stock')
            <thead><tr><th>SKU</th><th>Product</th><th>Available</th><th>Minimum stock</th><th>Status</th></tr></thead>
            <tbody>@forelse($items as $product)<tr><td>{{ $product->sku }}</td><td><strong>{{ $product->name }}</strong><div class="row-sub">{{ $product->categoryRecord?->name ?? $product->category }}</div></td><td>{{ (int) $product->available_quantity }} {{ $product->unit }}</td><td>{{ $product->minimum_stock }} {{ $product->unit }}</td><td><span class="badge {{ (int) $product->available_quantity === 0 ? 'red' : '' }}">{{ (int) $product->available_quantity === 0 ? 'Out of stock' : 'Low stock' }}</span></td></tr>@empty<tr><td colspan="5" style="padding:26px;text-align:center;color:#71848b">No products are below their minimum stock level.</td></tr>@endforelse</tbody>
        @else
            <thead><tr><th>Product</th><th>SKU</th><th>Batch</th><th>Units</th><th>Expiry date</th><th>{{ $type === 'expired-items' ? 'Days past expiry' : 'Days remaining' }}</th></tr></thead>
            <tbody>@forelse($items as $batch)<tr><td><strong>{{ $batch->product->name }}</strong></td><td>{{ $batch->product->sku }}</td><td>{{ $batch->batch_number }}</td><td>{{ $batch->quantity }} {{ $batch->product->unit }}</td><td>{{ $batch->expires_at->format('d M, Y') }}</td><td><span class="badge {{ $type === 'expired-items' ? 'red' : '' }}">{{ $type === 'expired-items' ? $batch->expires_at->diffInDays(today()) : today()->diffInDays($batch->expires_at) }}</span></td></tr>@empty<tr><td colspan="6" style="padding:26px;text-align:center;color:#71848b">No matching inventory batches found.</td></tr>@endforelse</tbody>
        @endif
    </table></div>
</section>
@endsection
