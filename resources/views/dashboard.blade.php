@extends('layouts.app')
@section('title', 'Dashboard')
@section('section', 'Dashboard')
@section('content')
@php
    $chartTotal = max($productCount, 1);
    $inEnd  = round($inStockCount / $chartTotal * 360, 2);
    $lowEnd = round(($inStockCount + $lowStockCount) / $chartTotal * 360, 2);
    $inPercent  = round($inStockCount  / $chartTotal * 100);
    $lowPercent = round($lowStockCount / $chartTotal * 100);
    $outPercent = round($outOfStockCount / $chartTotal * 100);
@endphp
<style>
/* ── Welcome banner ───────────────────────────────────────── */
.db-welcome{
    display:flex;align-items:center;justify-content:space-between;gap:16px;
    background:linear-gradient(135deg,#0d4a4c 0%,#116b62 55%,#1a8870 100%);
    border-radius:14px;padding:26px 28px;margin-bottom:22px;
    box-shadow:0 4px 20px #0d4a4c28;
}
.db-welcome-text .db-eyebrow{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#a3d9c8;margin-bottom:6px}
.db-welcome-text h1{margin:0 0 5px;font-size:22px;font-weight:800;color:#fff;line-height:1.2}
.db-welcome-text p{margin:0;font-size:12px;color:#b8ddd5}
.db-date-chip{flex-shrink:0;background:#ffffff18;border:1px solid #ffffff28;color:#d8f0ea;
    font-size:11px;font-weight:600;padding:8px 14px;border-radius:8px;white-space:nowrap;backdrop-filter:blur(4px)}

/* ── 4 KPI cards ──────────────────────────────────────────── */
.db-kpi-row{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:18px}
.db-kpi{
    position:relative;display:block;overflow:hidden;
    background:#fff;border:1px solid #e3eaeb;border-radius:12px;
    padding:18px 18px 16px;color:inherit;
    transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease;
    box-shadow:0 1px 4px #173b420a;
}
.db-kpi:hover{transform:translateY(-3px);box-shadow:0 8px 22px #173b4214;border-color:var(--kpi-accent)}
.db-kpi-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
.db-kpi-label{font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#6c7e84}
.db-kpi-icon{width:32px;height:32px;border-radius:8px;display:grid;place-items:center;
    background:var(--kpi-soft);color:var(--kpi-accent);font-size:14px;font-weight:700}
.db-kpi-value{font-size:30px;font-weight:800;line-height:1;color:var(--kpi-accent);letter-spacing:-0.03em}
.db-kpi-note{margin-top:6px;font-size:10px;color:#8a999e}
.db-kpi-bar{position:absolute;bottom:0;left:0;right:0;height:3px;background:var(--kpi-accent);opacity:.25;border-radius:0 0 12px 12px}

/* ── Main 2-col grid ──────────────────────────────────────── */
.db-main{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(280px,.7fr);gap:16px;margin-bottom:18px}

/* ── Donut panel ──────────────────────────────────────────── */
.db-panel{background:#fff;border:1px solid #e3eaeb;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px #173b420a}
.db-panel-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #edf1f2}
.db-panel-head h2{margin:0;font-size:14px;font-weight:700;color:#1f3138}
.db-panel-head span{font-size:11px;color:#829399}
.db-donut-body{display:grid;grid-template-columns:minmax(170px,.8fr) 1fr;align-items:center;gap:0;padding:24px 20px}
.db-donut-wrap{display:grid;place-items:center;position:relative;min-height:190px}
.db-donut{width:170px;aspect-ratio:1;border-radius:50%;position:relative;
    background:{{ $productCount
        ? 'conic-gradient(#25846e 0deg '.$inEnd.'deg,#e2a32b '.$inEnd.'deg '.$lowEnd.'deg,#d95750 '.$lowEnd.'deg 360deg)'
        : '#e8eeee' }};
}
.db-donut::after{content:"";position:absolute;inset:26px;background:#fff;border-radius:50%}
.db-donut-center{position:absolute;z-index:1;text-align:center}
.db-donut-center strong{display:block;font-size:28px;font-weight:800;line-height:1.1;color:#1f3138}
.db-donut-center span{font-size:10px;color:#829399}
.db-legend{display:grid;gap:14px;padding-left:8px}
.db-legend-item{}
.db-legend-row{display:grid;grid-template-columns:8px 1fr auto;gap:9px;align-items:center;font-size:12px;color:#536b73}
.db-legend-dot{width:8px;height:8px;border-radius:50%;background:var(--dot)}
.db-legend-row strong{font-size:13px;font-weight:700;color:#1f3138}
.db-legend-sub{font-size:10px;color:#8a999e;margin:3px 0 5px 17px}
.db-legend-bar{height:4px;border-radius:4px;background:#eef2f2;overflow:hidden;margin-left:17px}
.db-legend-bar span{display:block;height:100%;width:var(--w);background:var(--dot);border-radius:4px}

/* ── Right metric stack ───────────────────────────────────── */
.db-metric-stack{display:grid;grid-template-columns:1fr 1fr;gap:12px;align-content:start}
.db-metric{
    position:relative;overflow:hidden;border-radius:12px;
    padding:18px 16px 16px;color:#fff;min-height:120px;
    box-shadow:0 2px 10px #0003;
}
.db-metric a{color:#fff}
.db-metric-label{font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
    color:#ffffffcc;max-width:calc(100% - 42px);line-height:1.4}
.db-metric-value{margin-top:10px;font-size:22px;font-weight:800;line-height:1.1;color:#fff;
    padding-right:44px;word-break:break-word}
.db-metric-value.sm{font-size:16px}
.db-metric-sub{margin-top:5px;font-size:9px;color:#ffffffaa}
.db-metric-icon{position:absolute;right:12px;top:50%;transform:translateY(-50%);
    width:34px;height:34px;border-radius:50%;background:#ffffff20;
    display:grid;place-items:center;font-size:14px;font-weight:700;color:#fff}
.db-metric--teal{background:linear-gradient(135deg,#075c59,#0a7a70)}
.db-metric--blue{background:linear-gradient(135deg,#0a6fa0,#0d8ec8)}
.db-metric--red{background:linear-gradient(135deg,#c43232,#e04545)}
.db-metric--amber{background:linear-gradient(135deg,#b87008,#da8f10)}
.db-metric-link{display:block;transition:transform .18s,filter .18s}
.db-metric-link:hover{transform:translateY(-3px);filter:brightness(.93)}

/* ── Quick actions bar ────────────────────────────────────── */
.db-actions{background:#fff;border:1px solid #e3eaeb;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px #173b420a}
.db-actions-head{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #edf1f2}
.db-actions-head h2{margin:0;font-size:13px;font-weight:700;color:#1f3138}
.db-actions-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr))}
.db-action-link{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;
    padding:16px 10px;border-right:1px solid #edf1f2;color:#304b52;
    transition:background .15s,color .15s;text-align:center}
.db-action-link:last-child{border-right:0}
.db-action-link:hover{background:#f4f8f7;color:#0d6a62}
.db-action-icon{width:36px;height:36px;border-radius:9px;background:#edf4f2;color:#177c78;
    display:grid;place-items:center;font-size:16px;transition:background .15s}
.db-action-link:hover .db-action-icon{background:#d5ede8}
.db-action-name{font-size:11px;font-weight:600}
.db-action-hint{font-size:9px;color:#8a9ea5}

/* ── Responsive ───────────────────────────────────────────── */
@media(max-width:980px){
    .db-kpi-row{grid-template-columns:repeat(2,1fr)}
    .db-main{grid-template-columns:1fr}
    .db-actions-grid{grid-template-columns:repeat(3,1fr)}
    .db-action-link:nth-child(3){border-right:0}
    .db-action-link:nth-child(4){border-top:1px solid #edf1f2}
    .db-action-link:nth-child(5){border-top:1px solid #edf1f2;border-right:0}
}
@media(max-width:600px){
    .db-welcome{flex-direction:column;align-items:flex-start;padding:18px}
    .db-kpi-row{grid-template-columns:1fr 1fr;gap:8px}
    .db-donut-body{grid-template-columns:1fr;padding:16px}
    .db-donut-wrap{min-height:170px}
    .db-donut{width:150px}
    .db-metric-stack{grid-template-columns:1fr 1fr;gap:8px}
    .db-actions-grid{grid-template-columns:repeat(2,1fr)}
    .db-action-link:nth-child(2n){border-right:0}
    .db-action-link{border-top:1px solid #edf1f2}
    .db-action-link:nth-child(1),.db-action-link:nth-child(2){border-top:0}
}
</style>

{{-- ── Welcome Banner ── --}}
<div class="db-welcome">
    <div class="db-welcome-text">
        <div class="db-eyebrow">Davao Medcare · Operations</div>
        <h1>Welcome back, {{ $user?->name ?? 'there' }} 👋</h1>
        <p>Here is your inventory position for today.</p>
    </div>
    <div class="db-date-chip">{{ now()->format('l, F j, Y') }}</div>
</div>

{{-- ── KPI Row ── --}}
<div class="db-kpi-row">
    <a class="db-kpi" href="{{ route('products.index') }}" style="--kpi-accent:#177c78;--kpi-soft:#e5f2f0">
        <div class="db-kpi-top">
            <span class="db-kpi-label">Total Products</span>
            <span class="db-kpi-icon">▤</span>
        </div>
        <div class="db-kpi-value">{{ number_format($productCount) }}</div>
        <div class="db-kpi-note">Active in catalog</div>
        <div class="db-kpi-bar"></div>
    </a>
    <a class="db-kpi" href="{{ route('products.index', ['stock_status' => 'in-stock']) }}" style="--kpi-accent:#25846e;--kpi-soft:#e6f3ed">
        <div class="db-kpi-top">
            <span class="db-kpi-label">In Stock</span>
            <span class="db-kpi-icon">✓</span>
        </div>
        <div class="db-kpi-value">{{ number_format($inStockCount) }}</div>
        <div class="db-kpi-note">Above reorder level</div>
        <div class="db-kpi-bar"></div>
    </a>
    <a class="db-kpi" href="{{ route('products.index', ['stock_status' => 'low-stock']) }}" style="--kpi-accent:#c47d10;--kpi-soft:#fff3df">
        <div class="db-kpi-top">
            <span class="db-kpi-label">Low Stock</span>
            <span class="db-kpi-icon">!</span>
        </div>
        <div class="db-kpi-value">{{ number_format($lowStockCount) }}</div>
        <div class="db-kpi-note">Needs restocking</div>
        <div class="db-kpi-bar"></div>
    </a>
    <a class="db-kpi" href="{{ route('products.index', ['stock_status' => 'out-of-stock']) }}" style="--kpi-accent:#cc4a44;--kpi-soft:#fbe9e7">
        <div class="db-kpi-top">
            <span class="db-kpi-label">Out of Stock</span>
            <span class="db-kpi-icon">×</span>
        </div>
        <div class="db-kpi-value">{{ number_format($outOfStockCount) }}</div>
        <div class="db-kpi-note">Unavailable products</div>
        <div class="db-kpi-bar"></div>
    </a>
</div>

{{-- ── Main Grid ── --}}
<div class="db-main">

    {{-- Donut chart panel --}}
    <div class="db-panel">
        <div class="db-panel-head">
            <h2>Inventory Status</h2>
            <span>Product count by stock level</span>
        </div>
        <div class="db-donut-body">
            <div class="db-donut-wrap">
                <div class="db-donut" role="img" aria-label="{{ $inStockCount }} in stock, {{ $lowStockCount }} low, {{ $outOfStockCount }} out"></div>
                <div class="db-donut-center">
                    <strong>{{ number_format($productCount) }}</strong>
                    <span>products</span>
                </div>
            </div>
            <div class="db-legend">
                <div class="db-legend-item">
                    <div class="db-legend-row" style="--dot:#25846e">
                        <span class="db-legend-dot"></span><span>In stock</span><strong>{{ $inStockCount }}</strong>
                    </div>
                    <div class="db-legend-sub">{{ $inPercent }}% of catalog</div>
                    <div class="db-legend-bar" style="--dot:#25846e"><span style="--w:{{ $inPercent }}%"></span></div>
                </div>
                <div class="db-legend-item">
                    <div class="db-legend-row" style="--dot:#e2a32b">
                        <span class="db-legend-dot"></span><span>Low stock</span><strong>{{ $lowStockCount }}</strong>
                    </div>
                    <div class="db-legend-sub">{{ $lowPercent }}% of catalog</div>
                    <div class="db-legend-bar" style="--dot:#e2a32b"><span style="--w:{{ $lowPercent }}%"></span></div>
                </div>
                <div class="db-legend-item">
                    <div class="db-legend-row" style="--dot:#d95750">
                        <span class="db-legend-dot"></span><span>Out of stock</span><strong>{{ $outOfStockCount }}</strong>
                    </div>
                    <div class="db-legend-sub">{{ $outPercent }}% of catalog</div>
                    <div class="db-legend-bar" style="--dot:#d95750"><span style="--w:{{ $outPercent }}%"></span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Metric cards --}}
    <div class="db-metric-stack">
        <div class="db-metric db-metric--teal">
            <div class="db-metric-label">Total Inventory Value</div>
            <div class="db-metric-value sm">₱{{ number_format($inventoryValue, 2) }}</div>
            <div class="db-metric-sub">At selling price</div>
            <span class="db-metric-icon">₱</span>
        </div>
        <div class="db-metric db-metric--blue">
            <div class="db-metric-label">Active Stock Lots</div>
            <div class="db-metric-value">{{ number_format($activeLots) }}</div>
            <div class="db-metric-sub">Available batches</div>
            <span class="db-metric-icon">▣</span>
        </div>
        <div class="db-metric db-metric--red">
            <div class="db-metric-label">Expiring in 30 Days</div>
            <div class="db-metric-value">{{ number_format($expiring30DayCount) }}</div>
            <div class="db-metric-sub">Batches to review</div>
            <span class="db-metric-icon">!</span>
        </div>
        <a class="db-metric db-metric--amber db-metric-link" href="{{ route('adjustments.index') }}">
            <div class="db-metric-label">Pending Adjustments</div>
            <div class="db-metric-value">{{ number_format($pendingAdjustments) }}</div>
            <div class="db-metric-sub">{{ $user?->role === 'admin' ? 'Awaiting review' : 'Your requests' }}</div>
            <span class="db-metric-icon">↗</span>
        </a>
    </div>
</div>

{{-- ── Quick Actions ── --}}
<div class="db-actions">
    <div class="db-actions-head">
        <h2>Quick Actions</h2>
    </div>
    <div class="db-actions-grid">
        <a class="db-action-link" href="{{ route('products.create') }}">
            <span class="db-action-icon">+</span>
            <span class="db-action-name">Add Product</span>
            <span class="db-action-hint">New catalog item</span>
        </a>
        <a class="db-action-link" href="{{ route('stock-ins.create') }}">
            <span class="db-action-icon">↓</span>
            <span class="db-action-name">Stock In</span>
            <span class="db-action-hint">Record delivery</span>
        </a>
        <a class="db-action-link" href="{{ route('sales-orders.create') }}">
            <span class="db-action-icon">◎</span>
            <span class="db-action-name">New Order</span>
            <span class="db-action-hint">Create sales order</span>
        </a>
        <a class="db-action-link" href="{{ route('purchase-orders.index') }}">
            <span class="db-action-icon">▧</span>
            <span class="db-action-name">Purchase Orders</span>
            <span class="db-action-hint">View & manage POs</span>
        </a>
        <a class="db-action-link" href="{{ route('reports.index') }}">
            <span class="db-action-icon">▥</span>
            <span class="db-action-name">Reports</span>
            <span class="db-action-hint">Inventory reports</span>
        </a>
    </div>
</div>
@endsection
