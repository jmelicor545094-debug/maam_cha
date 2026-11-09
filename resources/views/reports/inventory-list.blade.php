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
    .report-header {
        display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;
        margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #e7edf0;
    }
    .report-header h1 {
        margin: 6px 0 5px; font-size: 2rem; color: #1f3138;
    }
    .report-crumb { font-size: 0.74rem; color: #7c8d92; letter-spacing: 0.04em; }
    .report-summary { color: #687d83; font-size: 0.92rem; margin-top: 6px; }
    .report-actions { display: flex; align-items: center; gap: 10px; }
    .report-actions .btn {
        display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; height: 40px; padding: 0 14px;
        background: #f3f6f7; border: 1px solid #dfe7ea; color: #1d2d33;
    }
    .report-panel {
        background: #fff; border: 1px solid #e7edf0; border-radius: 16px; overflow: hidden;
        box-shadow: 0 2px 10px rgba(17, 36, 46, 0.04);
    }
    .report-panel .panel-head {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 18px 20px; background: linear-gradient(180deg, #fbfcfd 0%, #f7f9fa 100%);
        border-bottom: 1px solid #edf1f3;
    }
    .report-panel .panel-head h2 { margin: 0; font-size: 1.08rem; color: #1f3138; }
    .report-count {
        display: inline-flex; align-items: center; min-height: 30px; padding: 0 10px; border-radius: 999px;
        background: #eaf5f4; color: #1f7260; font-size: 0.73rem; font-weight: 700;
    }
    .report-table { width: 100%; border-collapse: collapse; }
    .report-table thead th {
        background: #f4f7f8; color: #5d737a; text-align: left; text-transform: uppercase; letter-spacing: 0.08em;
        font-size: 0.7rem; padding: 12px 16px; font-weight: 700;
    }
    .report-table tbody td {
        padding: 14px 16px; border-top: 1px solid #edf1f3; color: #243840; vertical-align: top;
    }
    .report-table tbody tr:hover { background: #fafcfc; }
    .badge {
        display: inline-flex; align-items: center; justify-content: center; min-height: 28px; padding: 4px 10px;
        border-radius: 999px; font-size: 0.72rem; font-weight: 700;
        background: #edf4f6; color: #214b58;
    }
    .badge.red { background: #fdeceb; color: #c6463a; }
    .badge.amber { background: #fff4df; color: #9f6f17; }
    .row-sub { color: #69818b; font-size: 0.8rem; margin-top: 4px; }
    .empty-row { padding: 26px 16px; text-align: center; color: #75888d; }
    @media(max-width:600px){
        .report-header { flex-direction: column; }
        .report-actions { width: 100%; }
        .report-actions .btn { flex: 1; }
    }
</style>

<div class="report-header">
    <div>
        <div class="report-crumb">Dashboard / Inventory Reports / {{ $title }}</div>
        <h1>{{ $title }}</h1>
        <div class="report-summary">{{ $description }}</div>
    </div>
    <div class="report-actions">
        <a class="btn" href="{{ route('reports.index') }}">All reports</a>
        @if($type === 'expired-items' && auth()->user()->role === 'admin')
            <a class="btn" href="{{ route('expired-stock.index') }}">Review cleanup</a>
        @endif
    </div>
</div>

<section class="report-panel">
    <div class="panel-head">
        <h2>{{ $title }} register</h2>
        <span class="report-count">{{ $items->count() }} {{ $items->count() === 1 ? 'record' : 'records' }}</span>
    </div>
    <div class="table-wrap">
        <table class="report-table">
            @if($type === 'low-stock')
                <thead>
                    <tr><th>SKU</th><th>Product</th><th>Available</th><th>Minimum stock</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($items as $product)
                        <tr>
                            <td>{{ $product->sku }}</td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                <div class="row-sub">{{ $product->categoryRecord?->name ?? $product->category }}</div>
                            </td>
                            <td>{{ (int) $product->available_quantity }} {{ $product->unit }}</td>
                            <td>{{ $product->minimum_stock }} {{ $product->unit }}</td>
                            <td>
                                <span class="badge {{ (int) $product->available_quantity === 0 ? 'red' : 'amber' }}">
                                    {{ (int) $product->available_quantity === 0 ? 'Out of stock' : 'Low stock' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-row">No products are below their minimum stock level.</td></tr>
                    @endforelse
                </tbody>
            @else
                <thead>
                    <tr><th>Product</th><th>SKU</th><th>Batch</th><th>Units</th><th>Expiry date</th><th>{{ $type === 'expired-items' ? 'Days past expiry' : 'Days remaining' }}</th></tr>
                </thead>
                <tbody>
                    @forelse($items as $batch)
                        <tr>
                            <td><strong>{{ $batch->product->name }}</strong></td>
                            <td>{{ $batch->product->sku }}</td>
                            <td>{{ $batch->batch_number }}</td>
                            <td>{{ $batch->quantity }} {{ $batch->product->unit }}</td>
                            <td>{{ $batch->expires_at->format('d M, Y') }}</td>
                            <td>
                                <span class="badge {{ $type === 'expired-items' ? 'red' : 'amber' }}">
                                    {{ $type === 'expired-items' ? $batch->expires_at->diffInDays(today()) : today()->diffInDays($batch->expires_at) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-row">No matching inventory batches found.</td></tr>
                    @endforelse
                </tbody>
            @endif
        </table>
    </div>
</section>
@endsection
