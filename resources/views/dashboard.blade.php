@extends('layouts.app')
@section('title', 'Dashboard')
@section('section', 'Dashboard')
@section('content')
@php
    $chartTotal = max($productCount, 1);
    $inEnd = round($inStockCount / $chartTotal * 360, 2);
    $lowEnd = round(($inStockCount + $lowStockCount) / $chartTotal * 360, 2);
    $inPercent = round($inStockCount / $chartTotal * 100);
    $lowPercent = round($lowStockCount / $chartTotal * 100);
    $outPercent = round($outOfStockCount / $chartTotal * 100);
@endphp
<style>
    .welcome-row{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;margin-bottom:22px}.welcome-row h1{margin:5px 0 3px;font-size:25px}.welcome-copy{font-size:13px;color:#71848b}.today-chip{font-size:11px;color:#52737b;background:#e9f1ef;border:1px solid #dce9e5;padding:8px 11px;border-radius:4px;white-space:nowrap}.welcome-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;flex-wrap:wrap}
    .inventory-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px;margin-bottom:18px}.inventory-stat{position:relative;display:block;overflow:hidden;background:#fff;border:1px solid #e1e8e9;border-radius:6px;padding:16px 17px 15px;min-height:116px;color:inherit;transition:transform .16s ease,box-shadow .16s ease,border-color .16s ease}.inventory-stat:before{content:"";position:absolute;inset:0 auto 0 0;width:3px;background:var(--accent)}.inventory-stat:hover{transform:translateY(-2px);border-color:var(--accent);box-shadow:0 7px 18px #173b4212}.inventory-stat:focus-visible{outline:3px solid #16796b40;outline-offset:2px}.inventory-stat-top{display:flex;align-items:center;justify-content:space-between;color:#647a82;font-size:11px}.inventory-stat-icon{display:grid;place-items:center;width:30px;height:30px;border-radius:6px;background:var(--soft);color:var(--accent);font-size:15px}.inventory-stat-value{font-size:27px;font-weight:700;line-height:1;margin-top:12px;color:#1d3440}.inventory-stat-note{margin-top:6px;font-size:10px;color:#8a999e}
    .dashboard-grid{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(300px,.75fr);gap:15px}.dash-panel{background:#fff;border:1px solid #e1e8e9;border-radius:6px;overflow:hidden}.dash-panel-head{display:flex;justify-content:space-between;align-items:center;padding:15px 17px;border-bottom:1px solid #e8edee}.dash-panel-head h2{font-size:14px;margin:0}.dash-panel-head span{font-size:11px;color:#829399}.status-body{display:grid;grid-template-columns:minmax(190px,.85fr) minmax(180px,1fr);align-items:center;gap:14px;padding:24px 22px}.donut-wrap{display:grid;place-items:center;position:relative;min-height:205px}.donut{width:184px;aspect-ratio:1;border-radius:50%;background:conic-gradient(#25846e 0deg {{ $inEnd }}deg,#e2a32b {{ $inEnd }}deg {{ $lowEnd }}deg,#d95750 {{ $lowEnd }}deg 360deg);position:relative}.donut:after{content:"";position:absolute;inset:24px;background:#fff;border-radius:50%}.donut-center{position:absolute;z-index:1;text-align:center}.donut-center strong{display:block;font-size:29px;line-height:1.1}.donut-center span{color:#819197;font-size:10px}.legend{display:grid;gap:13px}.legend-row{display:grid;grid-template-columns:10px 1fr auto;gap:9px;align-items:center;font-size:12px;color:#536b73}.legend-dot{width:9px;height:9px;border-radius:50%;background:var(--dot)}.legend-row strong{color:#213943}.legend-hint{font-size:10px;color:#8a999e;margin-top:3px}
    .side-stack{display:grid;grid-template-columns:1fr 1fr;gap:12px;align-content:start}.value-panel{grid-column:auto;background:#fff;color:#20343b;border:1px solid #e1e8e9;min-height:112px}.value-panel .dash-panel-head{border-color:#e8edee}.value-panel .dash-panel-head h2{color:#2a4148}.value-content{padding:13px 15px;display:block}.value-caption{font-size:10px;color:#748990}.value-number{font-size:21px;font-weight:700;margin-top:6px;white-space:nowrap;color:#1d3440}.value-divider{border:0;padding:0;margin-top:7px}.value-divider .value-number{font-size:11px;font-weight:600;margin-top:2px;color:#6f8589}.mini-panel{min-height:112px;padding:15px 16px}.mini-panel-label{font-size:10px;color:#748990;text-transform:uppercase}.mini-panel-value{font-size:25px;font-weight:700;margin-top:13px;color:#1c4852}.mini-panel-sub{font-size:10px;color:#87989d;margin-top:4px}.metric-link{display:block;transition:transform .16s ease,box-shadow .16s ease}.metric-link:hover{transform:translateY(-2px);box-shadow:0 6px 18px #173b4212}.status-progress{height:5px;margin-top:8px;overflow:hidden;border-radius:4px;background:#eef2f2}.status-progress span{display:block;height:100%;width:var(--progress);background:var(--dot);border-radius:4px}.legend-item{padding:3px 0}.legend{gap:12px}
    .workflow-panel{margin-bottom:16px;background:#fff;border:1px solid #e1e8e9;border-radius:6px;overflow:hidden}.workflow-panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 17px;border-bottom:1px solid #e8edee}.workflow-panel-head h2{font-size:14px;margin:0}.workflow-panel-head span{font-size:11px;color:#829399}.workflow-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr))}.workflow-link{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 17px;border-right:1px solid #edf0f1;color:#304b52}.workflow-link:last-child{border-right:0}.workflow-link:hover{background:#f7faf9}.workflow-name{font-size:12px;font-weight:600}.workflow-hint{font-size:10px;color:#829399;margin-top:4px}.workflow-count{display:grid;place-items:center;min-width:30px;height:30px;padding:0 8px;border-radius:5px;background:#eef4f2;color:#267a63;font-size:13px;font-weight:700}.workflow-count.attention{background:#fff1dc;color:#a76b0a}
    @media(max-width:980px){.inventory-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.dashboard-grid{grid-template-columns:1fr}.side-stack{grid-template-columns:1fr 1fr}.expiry-panel{grid-column:1/-1}}@media(max-width:700px){.workflow-grid{grid-template-columns:1fr}.workflow-link{border-right:0;border-bottom:1px solid #edf0f1}.workflow-link:last-child{border-bottom:0}}@media(max-width:560px){.content{padding:18px 12px}.welcome-row{align-items:flex-start;flex-direction:column;gap:10px}.welcome-actions{justify-content:flex-start;width:100%}.inventory-stats{gap:8px}.inventory-stat{padding:13px;min-height:105px}.inventory-stat-value{font-size:24px}.status-body{grid-template-columns:1fr;padding:15px}.donut-wrap{min-height:185px}.donut{width:160px}.legend{grid-template-columns:1fr 1fr}.side-stack{gap:8px}.mini-panel{padding:12px}.mini-panel-value{font-size:21px}.value-content{padding:13px}.value-number{font-size:19px}}
</style>
<style>
    .metric-card{position:relative;display:block;overflow:hidden;min-height:112px;padding:17px 15px;border:0;border-radius:7px;color:#fff}
    .metric-card__label{max-width:calc(100% - 46px);font-size:9px;font-weight:700;line-height:1.35;text-transform:uppercase;color:#ffffffc9}
    .metric-card__value{margin-top:9px;font-size:23px;line-height:1;font-weight:700;color:#fff}
    .metric-card__sub{margin-top:6px;font-size:9px;color:#ffffffb8}
    .metric-card__icon{position:absolute;right:13px;top:50%;display:grid;place-items:center;width:34px;height:34px;transform:translateY(-50%);border-radius:50%;background:#ffffff26;color:#fff;font-size:14px;font-weight:700}
    .metric-card--value{background:#075c59}
    .metric-card--lots{background:#087fb2}
    .metric-card--expiry{background:#d83d3b}
    .metric-card--adjustments{background:#df8b08}
    a.metric-card{transition:transform .16s ease,filter .16s ease}
    a.metric-card:hover{transform:translateY(-2px);filter:brightness(.96)}
    a.metric-card:focus-visible{outline:3px solid #16796b60;outline-offset:2px}
    .inventory-stat{min-height:100px;padding:14px 16px;border-radius:7px}
    .inventory-stat:before{display:none}
    .inventory-stat-top{justify-content:flex-start;font-size:9px;font-weight:700;text-transform:uppercase;color:#6c7e83}
    .inventory-stat-icon{position:absolute;right:14px;top:50%;transform:translateY(-50%);width:34px;height:34px;border-radius:50%;font-size:14px}
    .inventory-stat-value{margin-top:8px;font-size:23px}
    .inventory-stat-note{display:none}
    .value-panel{grid-column:auto;min-height:112px}
    .value-content{display:block;padding:13px 15px}
    .value-caption{font-size:10px}
    .value-number{font-size:21px;margin-top:6px;white-space:nowrap}
    .value-divider{border:0;padding:0;margin-top:7px}
    .value-divider .value-number{font-size:11px;font-weight:600;margin-top:2px;color:#6f8589}
    .mini-panel{min-height:112px}
    .metric-link{display:block;transition:transform .16s ease,box-shadow .16s ease}
    .metric-link:hover{transform:translateY(-2px);box-shadow:0 6px 18px #173b4212}
    .workflow-panel{margin-top:16px}
    @media(max-width:560px){.value-content{padding:12px}.value-number{font-size:19px}}
</style>
<div class="welcome-row"><div><div class="eyebrow">Davao Medcare · Operations</div><h1>Welcome back, {{ $user?->name ?? 'there' }}</h1><div class="welcome-copy">Here is your inventory position for today.</div></div><div class="welcome-actions"><div class="today-chip">{{ now()->format('l, F j, Y') }}</div></div></div>
<div class="inventory-stats">
    <a class="inventory-stat" href="{{ route('products.index') }}" style="--accent:#177c78;--soft:#e5f2f0"><div class="inventory-stat-top">Total products <span class="inventory-stat-icon">▤</span></div><div class="inventory-stat-value">{{ number_format($productCount) }}</div><div class="inventory-stat-note">View product catalog</div></a>
    <a class="inventory-stat" href="{{ route('products.index', ['stock_status' => 'in-stock']) }}" style="--accent:#25846e;--soft:#e6f3ed"><div class="inventory-stat-top">In stock <span class="inventory-stat-icon">✓</span></div><div class="inventory-stat-value" style="color:#25846e">{{ number_format($inStockCount) }}</div><div class="inventory-stat-note">Above minimum quantity</div></a>
    <a class="inventory-stat" href="{{ route('products.index', ['stock_status' => 'low-stock']) }}" style="--accent:#d4931e;--soft:#fff3df"><div class="inventory-stat-top">Low stock <span class="inventory-stat-icon">!</span></div><div class="inventory-stat-value" style="color:#c38617">{{ number_format($lowStockCount) }}</div><div class="inventory-stat-note">At or below reorder level</div></a>
    <a class="inventory-stat" href="{{ route('products.index', ['stock_status' => 'out-of-stock']) }}" style="--accent:#d95750;--soft:#fbe9e7"><div class="inventory-stat-top">Out of stock <span class="inventory-stat-icon">×</span></div><div class="inventory-stat-value" style="color:#cc4e49">{{ number_format($outOfStockCount) }}</div><div class="inventory-stat-note">View unavailable products</div></a>
</div>
<div class="dashboard-grid">
    <div>
        <section class="dash-panel"><div class="dash-panel-head"><h2>Inventory status</h2><span>Product count by stock level</span></div><div class="status-body"><div class="donut-wrap"><div class="donut" role="img" aria-label="{{ $inStockCount }} in stock, {{ $lowStockCount }} low stock, {{ $outOfStockCount }} out of stock" style="background:{{ $productCount ? 'conic-gradient(#25846e 0deg '.$inEnd.'deg,#e2a32b '.$inEnd.'deg '.$lowEnd.'deg,#d95750 '.$lowEnd.'deg 360deg)' : '#e8eeee' }}"></div><div class="donut-center"><strong>{{ number_format($productCount) }}</strong><span>products</span></div></div><div class="legend">
            <div class="legend-item"><div class="legend-row"><span class="legend-dot" style="--dot:#25846e"></span><span>In stock</span><strong>{{ $inStockCount }}</strong></div><div class="legend-hint">{{ $inPercent }}% of catalog</div><div class="status-progress" style="--dot:#25846e;--progress:{{ $inPercent }}%"><span></span></div></div>
            <div class="legend-item"><div class="legend-row"><span class="legend-dot" style="--dot:#e2a32b"></span><span>Low stock</span><strong>{{ $lowStockCount }}</strong></div><div class="legend-hint">{{ $lowPercent }}% of catalog</div><div class="status-progress" style="--dot:#e2a32b;--progress:{{ $lowPercent }}%"><span></span></div></div>
            <div class="legend-item"><div class="legend-row"><span class="legend-dot" style="--dot:#d95750"></span><span>Out of stock</span><strong>{{ $outOfStockCount }}</strong></div><div class="legend-hint">{{ $outPercent }}% of catalog</div><div class="status-progress" style="--dot:#d95750;--progress:{{ $outPercent }}%"><span></span></div></div>
        </div></div></section>
    </div>
    <div class="side-stack">
        <section class="metric-card metric-card--value"><div class="metric-card__label">Total inventory value</div><div class="metric-card__value">₱{{ number_format($inventoryValue, 2) }}</div><div class="metric-card__sub">At selling price</div><span class="metric-card__icon" aria-hidden="true">₱</span></section>
        <section class="metric-card metric-card--lots"><div class="metric-card__label">Active stock lots</div><div class="metric-card__value">{{ number_format($activeLots) }}</div><div class="metric-card__sub">Available batches</div><span class="metric-card__icon" aria-hidden="true">▣</span></section>
        <section class="metric-card metric-card--expiry"><div class="metric-card__label">Expiring in 30 days</div><div class="metric-card__value">{{ number_format($expiring30DayCount) }}</div><div class="metric-card__sub">Batches to review</div><span class="metric-card__icon" aria-hidden="true">!</span></section>
        <a class="metric-card metric-card--adjustments" href="{{ route('adjustments.index') }}"><div class="metric-card__label">Pending adjustments</div><div class="metric-card__value">{{ number_format($pendingAdjustments) }}</div><div class="metric-card__sub">{{ $user?->role === 'admin' ? 'Awaiting review' : 'Your requests' }}</div><span class="metric-card__icon" aria-hidden="true">↗</span></a>
    </div>
</div>
@endsection
