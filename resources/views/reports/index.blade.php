@extends('layouts.app')
@section('title', 'Reports')
@section('section', 'Reports')
@section('content')
<style>
    .report-page { color: #1d2d33; }
    .report-header {
        display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;
        margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e7edf0;
    }
    .report-header h1 {
        margin: 6px 0 4px; font-size: 2.1rem; line-height: 1.2; color: #1f3138;
    }
    .report-subtitle {
        margin: 0; color: #657b83; font-size: 0.95rem;
    }
    .eyebrow {
        display: inline-block; font-size: 0.72rem; letter-spacing: 0.12em; text-transform: uppercase;
        color: #4d7b86; font-weight: 700;
    }
    .report-actions { display: flex; align-items: center; gap: 10px; }
    .report-actions .btn {
        display: inline-flex; align-items: center; justify-content: center; height: 42px; padding: 0 16px;
        border-radius: 10px; background: #f3f6f7; color: #1d2d33; border: 1px solid #dfe7ea;
    }
    .report-actions .btn:hover { background: #edf3f4; }
    .report-metrics {
        display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px;
    }
    .metric-card {
        background: #fff; border: 1px solid #e7edf0; border-radius: 16px; padding: 18px 18px 16px;
        box-shadow: 0 2px 10px rgba(17, 36, 46, 0.04);
    }
    .metric-label {
        color: #6a7d84; font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700;
    }
    .metric-value {
        margin-top: 10px; font-size: clamp(1.7rem, 2vw, 2.2rem); font-weight: 800; letter-spacing: -0.04em; color: #1e2f35;
    }
    .metric-note {
        margin-top: 8px; font-size: 0.8rem; color: #72848b;
    }
    .report-panel {
        background: #fff; border: 1px solid #e7edf0; border-radius: 16px; overflow: hidden;
        box-shadow: 0 2px 10px rgba(17, 36, 46, 0.04);
        margin-bottom: 20px;
    }
    .panel-header {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 18px 20px; border-bottom: 1px solid #edf1f3; background: linear-gradient(180deg, #fbfcfd 0%, #f7f9fa 100%);
    }
    .panel-header h2 {
        margin: 0; font-size: 1.12rem; color: #1f3138;
    }
    .subtle {
        color: #72848b; font-size: 0.8rem;
    }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th {
        background: #f4f7f8; color: #5d737a; font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase;
        text-align: left; padding: 12px 16px; font-weight: 700;
    }
    .data-table tbody td {
        padding: 14px 16px; border-top: 1px solid #edf1f3; color: #243840; font-size: 0.92rem; vertical-align: top;
    }
    .data-table tbody tr:hover { background: #fafcfc; }
    .badge {
        display: inline-flex; align-items: center; justify-content: center; min-height: 28px; padding: 5px 10px;
        border-radius: 999px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.03em;
        background: #eaf5f4; color: #1f7260;
    }
    .badge.green { background: #eaf8f1; color: #1f7a4a; }
    .badge.red { background: #fdeceb; color: #c6463a; }
    .badge.amber { background: #fff4df; color: #9f6f17; }
    .empty-row {
        padding: 24px 16px; text-align: center; color: #75888d; background: #fff;
    }
    @media (max-width: 980px) {
        .report-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 640px) {
        .report-header { flex-direction: column; }
        .report-actions { width: 100%; }
        .report-actions .btn { flex: 1; }
        .report-metrics { grid-template-columns: 1fr; }
    }

    /* ── Print styles ───────────────────────────────────────────── */
    @media print {
        /* Hide chrome: sidebar, topbars, nav */
        .sidebar,
        .topbar,
        .topbar-global,
        .report-actions { display: none !important; }

        /* Show print-only clinic header */
        .print-clinic-header { display: block !important; }

        /* Full-width layout – remove the grid shell */
        .shell { display: block !important; }
        .main  { display: block !important; }
        .content { padding: 0 !important; max-width: 100% !important; }

        /* Reset page chrome */
        body, html { background: #fff !important; margin: 0; padding: 0; }

        .report-page { padding: 0 !important; margin: 0 !important; width: 100% !important; }

        /* Report header */
        .report-header {
            margin-bottom: 14px !important;
            padding-bottom: 10px !important;
            border-bottom: 1.5px solid #c8d5d9 !important;
        }
        .report-header h1 { font-size: 1.5rem !important; }

        /* Metric cards: 4-across on print */
        .report-metrics {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 10px !important;
            margin-bottom: 16px !important;
        }
        .metric-card {
            border: 1px solid #d0dde1 !important;
            border-radius: 10px !important;
            padding: 12px 14px !important;
            box-shadow: none !important;
            break-inside: avoid;
        }
        .metric-value { font-size: 1.5rem !important; }

        /* Panels */
        .report-panel {
            border: 1px solid #d0dde1 !important;
            border-radius: 10px !important;
            box-shadow: none !important;
            margin-bottom: 14px !important;
            break-inside: avoid;
        }
        .panel-header {
            padding: 10px 14px !important;
            background: #f4f7f8 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .panel-header h2 { font-size: 0.95rem !important; }

        /* Tables */
        .data-table { font-size: 0.78rem !important; }
        .data-table thead th {
            padding: 8px 10px !important;
            font-size: 0.65rem !important;
            background: #edf1f3 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .data-table tbody td { padding: 8px 10px !important; }
        .data-table tbody tr:hover { background: transparent !important; }

        /* Badges */
        .badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: 0.65rem !important;
            padding: 2px 7px !important;
            min-height: 20px !important;
        }

        /* Page layout */
        @page {
            size: A4 landscape;
            margin: 14mm 12mm;
        }
    }
</style>

<div class="report-page">

    {{-- Print-only clinic header --}}
    <div class="print-clinic-header" style="display:none;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding-bottom:10px;margin-bottom:16px;border-bottom:2px solid #1f3138;">
            <div>
                <div style="font-size:1.3rem;font-weight:800;color:#1f3138;">Davao Medcare</div>
                <div style="font-size:0.78rem;color:#657b83;">Inventory Report</div>
            </div>
            <div style="text-align:right;font-size:0.78rem;color:#657b83;">
                Printed: {{ now()->format('F d, Y  h:i A') }}
            </div>
        </div>
    </div>

    <div class="report-header">
        <div>
            <div class="eyebrow">Management information</div>
            <h1>Inventory reports</h1>
            <p class="report-subtitle">Current stock, batch expiry, valuation and recent dispensed orders.</p>
        </div>
        <div class="report-actions">
            <button class="btn" onclick="window.print()" title="Print report">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M9 14h6v7H9v-7z"/></svg>
                Print report
            </button>
        </div>
    </div>

    <div class="report-metrics">
        <div class="metric-card">
            <div class="metric-label">Catalog items</div>
            <div class="metric-value">{{ $products->count() }}</div>
            <div class="metric-note">Active product records</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Available lots</div>
            <div class="metric-value">{{ $batches->count() }}</div>
            <div class="metric-note">With quantity on hand</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Low stock</div>
            <div class="metric-value">{{ $products->filter(fn($p) => $p->quantity <= $p->minimum_stock)->count() }}</div>
            <div class="metric-note">At or below reorder level</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Inventory cost value</div>
            <div class="metric-value" style="font-size: clamp(1.5rem, 2vw, 2rem);">₱{{ number_format($batches->sum(fn($batch) => $batch->quantity * $batch->unit_cost), 2) }}</div>
            <div class="metric-note">Batch quantity × unit cost</div>
        </div>
    </div>

    <section class="report-panel">
        <div class="panel-header">
            <h2>Current inventory by batch</h2>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product</th>
                        <th>Batch</th>
                        <th>Quantity</th>
                        <th>Unit cost</th>
                        <th>Expiry</th>
                        <th>Flag</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                        @php($expired = $batch->expires_at && $batch->expires_at->isPast())
                        @php($soon = $batch->expires_at && !$expired && $batch->expires_at->lte(now()->addDays(90)))
                        <tr>
                            <td>{{ $batch->product->sku }}</td>
                            <td>{{ $batch->product->name }}</td>
                            <td>{{ $batch->batch_number }}</td>
                            <td>{{ $batch->quantity }} {{ $batch->product->unit }}</td>
                            <td>₱{{ number_format($batch->unit_cost, 2) }}</td>
                            <td>{{ $batch->expires_at?->format('M d, Y') ?? '—' }}</td>
                            <td>
                                @if($expired)
                                    <span class="badge red">Expired</span>
                                @elseif($soon)
                                    <span class="badge amber">Expiring soon</span>
                                @else
                                    <span class="badge green">Current</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-row">No stock batches recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="report-panel">
        <div class="panel-header">
            <h2>Recent dispensed orders</h2>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Dispensed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->items->sum('quantity') }}</td>
                            <td>₱{{ number_format($order->total_amount, 2) }}</td>
                            <td>{{ $order->dispensed_at?->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-row">No dispensed orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="report-panel">
        <div class="panel-header">
            <h2>Recent stock movements</h2>
            <span class="subtle">Audit history</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Movement</th>
                        <th>Product</th>
                        <th>Batch</th>
                        <th>Qty</th>
                        <th>Reference</th>
                        <th>Recorded by</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td>{{ $movement->moved_at->format('M d, Y H:i') }}</td>
                            <td><span class="badge {{ $movement->movement_type === 'stock_in' ? 'green' : 'amber' }}">{{ str_replace('_', ' ', ucfirst($movement->movement_type)) }}</span></td>
                            <td>{{ $movement->product->name }}</td>
                            <td>{{ $movement->batch?->batch_number ?? '—' }}</td>
                            <td>{{ $movement->quantity }}</td>
                            <td>{{ $movement->reference_number }}</td>
                            <td>{{ $movement->user?->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-row">No stock movements recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="report-panel">
        <div class="panel-header">
            <h2>Supplier activity</h2>
            <span class="subtle">Approved delivery history</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Contact</th>
                        <th>Phone</th>
                        <th>Approved deliveries</th>
                        <th>Latest received</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->company_name }}</td>
                            <td>{{ $supplier->contact_person ?: '-' }}</td>
                            <td>{{ $supplier->phone ?: '-' }}</td>
                            <td>{{ $supplier->approved_deliveries_count }}</td>
                            <td>{{ $supplier->latest_approved_delivery ? \Illuminate\Support\Carbon::parse($supplier->latest_approved_delivery)->format('M d, Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-row">No supplier activity recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
