<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Medcare Operations') | Davao Medcare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{font-family:Inter,Segoe UI,Arial,sans-serif;color:#172b3b;background:#f5f7f8;font-synthesis:none}*{box-sizing:border-box}body{margin:0}a{color:inherit;text-decoration:none}button,input,select{font:inherit}.shell{display:grid;grid-template-columns:236px minmax(0,1fr);min-height:100vh}.sidebar{background:#183f50;color:#eaf3f3;padding:24px 16px}.brand{display:flex;align-items:center;gap:11px;padding:2px 10px 30px;font-size:15px;font-weight:700}.brand-mark{display:grid;place-items:center;background:#53b59a;color:#092f3a;width:34px;height:34px;border-radius:8px;font-weight:800}.nav-label{color:#a9c3c9;text-transform:uppercase;font-size:10px;letter-spacing:1px;padding:12px 10px 7px}.nav-link{display:flex;align-items:center;gap:12px;padding:10px;border-radius:5px;color:#d8e7e9;font-size:13px}.nav-link.active,.nav-link:hover{background:#28576a;color:#fff}.nav-icon{width:18px;text-align:center;color:#83c6b2}.main{min-width:0}.topbar{height:64px;background:#fff;border-bottom:1px solid #e2e8ea;display:flex;justify-content:space-between;align-items:center;padding:0 32px}.crumb{font-size:13px;color:#657b83}.profile{display:flex;align-items:center;gap:10px;font-size:12px}.avatar{display:grid;place-items:center;width:32px;height:32px;background:#e3f1eb;border-radius:50%;color:#28745e;font-weight:700}.content{max-width:1440px;margin:auto;padding:30px 32px}.page-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:18px;margin-bottom:24px}.eyebrow{font-size:11px;color:#638087;text-transform:uppercase;letter-spacing:.8px}.page-heading h1{font-size:24px;margin:5px 0 4px;letter-spacing:0}.muted{color:#71848b;font-size:13px}.stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}.stat,.panel{background:#fff;border:1px solid #e1e8e9;border-radius:6px}.stat{padding:17px 18px;min-height:112px}.stat-head{display:flex;justify-content:space-between;align-items:center;color:#667a82;font-size:12px}.stat-icon{width:29px;height:29px;border-radius:6px;display:grid;place-items:center;background:#e8f4f0;color:#267a63}.stat-value{font-size:26px;font-weight:700;margin:12px 0 2px}.stat-note{font-size:11px;color:#87999f}.columns{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(280px,.75fr);gap:16px}.panel{overflow:hidden}.panel-head{padding:16px 18px;border-bottom:1px solid #e8edee;display:flex;align-items:center;justify-content:space-between}.panel-head h2{font-size:14px;margin:0}.panel-body{padding:5px 18px}.row{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px 0;border-bottom:1px solid #edf0f1;font-size:12px}.row:last-child{border:0}.row-title{font-weight:600}.row-sub{font-size:11px;color:#839399;margin-top:4px}.badge{padding:4px 8px;border-radius:4px;font-size:10px;font-weight:700;background:#fff0dc;color:#9b6714}.badge.red{background:#fce8e7;color:#a6423c}.badge.green{background:#e5f3ed;color:#26785f}.btn{display:inline-flex;justify-content:center;align-items:center;gap:7px;border:1px solid #d5dfe1;background:#fff;color:#284b57;border-radius:4px;padding:9px 12px;font-size:12px;cursor:pointer}.btn.primary{background:#207860;border-color:#207860;color:#fff}.btn.danger{color:#a6423c}.toolbar{display:flex;gap:9px;align-items:center}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;text-align:left;white-space:nowrap}th{font-size:10px;text-transform:uppercase;letter-spacing:.45px;color:#74868d;font-weight:600;background:#fafbfb}th,td{padding:13px 16px;border-bottom:1px solid #edf0f1}td{font-size:12px}form.search{display:flex;gap:7px}input,select{border:1px solid #d8e1e2;border-radius:4px;padding:9px 10px;background:#fff;color:#263f49;font-size:12px;width:100%}.field{display:grid;gap:6px;margin:13px 0}.field label{font-size:12px;font-weight:600}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}.flash{background:#e5f3ed;color:#26785f;padding:11px 14px;border-radius:4px;font-size:12px;margin-bottom:16px}.errors{color:#a6423c;font-size:11px}.pagination{display:flex;justify-content:space-between;padding:14px 16px;font-size:12px;color:#657b83}
        @media(max-width:900px){.shell{grid-template-columns:68px minmax(0,1fr)}.sidebar{padding:20px 9px}.brand{padding:2px 8px 24px}.brand-name,.nav-label,.nav-text{display:none}.nav-link{justify-content:center;padding:12px 8px}.stats{grid-template-columns:repeat(2,minmax(0,1fr))}.columns{grid-template-columns:1fr}.content{padding:24px 18px}.topbar{padding:0 18px}}@media(max-width:560px){.stats{grid-template-columns:1fr 1fr;gap:8px}.stat{padding:13px;min-height:100px}.stat-value{font-size:22px}.page-heading{flex-direction:column}.toolbar{width:100%;justify-content:space-between}.form-grid{grid-template-columns:1fr}.content{padding:20px 12px}}
    </style>
    <style>
        :root{color:#20343b;background:#f3f6f7}
        body{background:#f3f6f7}
        .shell{grid-template-columns:248px minmax(0,1fr)}
        .sidebar{background:#103e42;padding:22px 14px}
        .brand{padding:3px 10px 24px;font-size:14px}
        .brand-mark{width:36px;height:36px;background:#a9ddc8;color:#104b45;border-radius:10px}
        .nav-label{padding:15px 11px 8px;color:#9bbab7;font-size:10px}
        .nav-link{min-height:39px;padding:9px 11px;border-radius:5px;color:#d7e7e4;transition:background .16s ease,color .16s ease}
        .nav-link.active{background:#1d6c68;color:white;box-shadow:inset 3px 0 #a9ddc8}
        .nav-link:hover{background:#1a5757;color:#fff}
        .nav-icon{color:#9bd4c3}
        .sidebar{display:flex;flex-direction:column}
        .sidebar>.nav-label:first-of-type{order:-2}
        .sidebar>a[href="/"]{order:-1}
        .nav-group{order:0}
        .nav-group summary{list-style:none;cursor:pointer}
        .nav-group summary::-webkit-details-marker{display:none}
        .nav-group[open]>summary{background:#164f4e;color:#fff}
        .nav-submenu{display:grid;gap:2px;padding:3px 0 7px 20px}
        .nav-sublink{display:block;padding:7px 10px;border-radius:4px;color:#c4d9d5;font-size:12px}
        .nav-sublink:hover,.nav-sublink.active{background:#195958;color:#fff}
        .sidebar>a.nav-link[href$="/products"],.sidebar>a.nav-link[href$="/stock-ins"],.sidebar>a.nav-link[href$="/adjustments"],.sidebar>a.nav-link[href$="/sales-orders"],.sidebar>a.nav-link[href$="/reports"],.sidebar>a.nav-link[href$="/expired-stock"],.sidebar>a.nav-link[href$="/permission-requests"],.sidebar>a.nav-link[href$="/users"]{display:none}
        .main{background:#f3f6f7}
        .topbar{height:62px;padding:0 30px;background:#fff;border-bottom-color:#e5ebec}
        .crumb{font-size:12px;color:#73858a}
        .profile{gap:9px;color:#526970}
        .avatar{background:#e3f2ec;color:#176c5d}
        .content{padding:30px;max-width:1500px}
        .page-heading h1,.welcome-row h1{font-size:25px;color:#1d343c}
        .eyebrow{color:#527c78}
        .muted{color:#788a8f}
        .panel,.dash-panel,.stat,.inventory-stat{border-color:#e3e9ea;border-radius:7px;box-shadow:0 1px 2px #173b4210}
        .panel-head,.dash-panel-head{padding:16px 19px;border-bottom-color:#edf0f1}
        .panel-head h2,.dash-panel-head h2{font-size:14px;color:#2a4148}
        .stat,.inventory-stat{transition:transform .16s ease,box-shadow .16s ease}
        .stat:hover,.inventory-stat:hover{transform:translateY(-2px);box-shadow:0 6px 18px #173b4212}
        .btn{min-height:36px;border-color:#dce5e6;color:#36565d;transition:background .15s ease,border-color .15s ease,transform .15s ease}
        .btn:hover{background:#f3f8f6;border-color:#b8d2ca}
        .btn.primary{background:#16796b;border-color:#16796b;color:#fff}
        .btn.primary:hover{background:#11695d;border-color:#11695d;transform:translateY(-1px)}
        input:focus,select:focus{outline:3px solid #16796b20;border-color:#278c7c}
        th{background:#f6f8f8;color:#667b80}
        tbody tr:hover{background:#f8fbfa}
        td{color:#334c53}
        .badge{border-radius:4px}
        .flash{border:1px solid #d1e9dd}
        @media(max-width:900px){.shell{grid-template-columns:68px minmax(0,1fr)}.content{padding:24px 18px}}
        @media(max-width:760px){
            .shell{grid-template-columns:minmax(0,1fr)}
            .sidebar{display:flex;flex-direction:row;flex-wrap:wrap;align-items:center;gap:4px;padding:9px 12px;overflow-x:auto;white-space:nowrap;scrollbar-width:thin}
            .brand{flex:0 0 auto;padding:0 10px 0 2px;margin-right:4px}
            .brand-name{display:block;font-size:12px}
            .brand-name small{display:none}
            .nav-label{display:none}
            .nav-link{display:flex;flex:0 0 auto;justify-content:flex-start;gap:7px;min-height:36px;padding:8px 10px;font-size:11px}
            .nav-text{display:inline}
            .nav-icon{width:14px}
            .nav-group{flex:0 0 auto}
            .nav-submenu{display:flex;flex-wrap:wrap;gap:3px;padding:5px 0 2px}
            .nav-sublink{padding:7px 9px;font-size:11px}
            .nav-group[open]{flex:1 0 100%}
            .topbar{height:54px;padding:0 14px}
            .profile{font-size:11px}
            .profile form .btn{min-height:32px;padding:6px 9px}
        }
        @media(max-width:560px){.content{padding:18px 12px}.topbar{padding:0 12px}.page-heading h1,.welcome-row h1{font-size:22px}}
    </style>
    <style>
        .shell{grid-template-columns:248px minmax(0,1fr);grid-template-rows:62px minmax(0,1fr)}
        .topbar-global{grid-column:1/-1;grid-row:1;justify-content:flex-start;gap:0;padding:0;height:62px;position:sticky;top:0;z-index:5}
        .topbar-global .topbar-brand{display:flex;align-items:center;gap:10px;flex:0 0 248px;height:100%;padding:0 24px;border-right:1px solid #edf0f1;color:#17494b;font-size:14px;font-weight:700}
        .topbar-global .brand-mark{flex:0 0 36px}
        .topbar-global .crumb{margin-left:28px}
        .topbar-global .profile{margin-left:auto;padding:0 25px}
        .sidebar{grid-column:1;grid-row:2;min-height:calc(100vh - 62px);overflow-y:auto}
        .sidebar>.brand{display:none}
        .main{grid-column:2;grid-row:2}
        .main>.topbar{display:none}
        @media(max-width:900px){.shell{grid-template-columns:68px minmax(0,1fr);grid-template-rows:62px minmax(0,1fr)}.topbar-global .topbar-brand{flex-basis:68px;justify-content:center;padding:0}.topbar-brand-name{display:none}.topbar-global .crumb{margin-left:18px}.topbar-global .profile{padding:0 16px}.sidebar{grid-column:1;grid-row:2;min-height:calc(100vh - 62px)}.main{grid-column:2;grid-row:2}}
        @media(max-width:760px){.shell{grid-template-columns:minmax(0,1fr);grid-template-rows:54px auto minmax(0,1fr)}.topbar-global{grid-column:1;grid-row:1;height:54px}.topbar-global .topbar-brand{flex-basis:150px;justify-content:flex-start;padding:0 12px;border-right:0}.topbar-brand-name{display:block;font-size:12px}.topbar-global .crumb{display:none}.topbar-global .profile{padding:0 12px}.sidebar{grid-column:1;grid-row:2;min-height:0}.main{grid-column:1;grid-row:3}}
        @media(max-width:560px){.topbar-global .profile>span:nth-child(2){display:none}.topbar-global .profile{gap:7px}.topbar-global .profile form .btn{min-height:32px;padding:6px 9px}}
    </style>
</head>
<body>
<div class="shell">
    <header class="topbar topbar-global"><a class="topbar-brand" href="{{ route('dashboard') }}"><span class="brand-mark brand-mark--logo"><img src="/images/logo.png" alt="Davao MedCare" class="brand-logo-img"></span><span class="topbar-brand-name">Davao Medcare</span></a><span class="crumb">Operations / @yield('section', 'Overview')</span><div class="profile">@auth <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span>{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role) }}</span><form method="post" action="{{ route('logout') }}">@csrf<button class="btn" type="submit" title="Sign out">Sign out</button></form>@else<a class="btn" href="{{ route('login') }}">Sign in</a>@endauth</div></header>
    <aside class="sidebar">
        <div class="brand brand--with-logo"><span class="brand-mark brand-mark--logo"><img src="/images/logo.png" alt="Davao MedCare" class="brand-logo-img"></span><span class="brand-name">Davao Medcare<br><small style="font-weight:400;color:#b8d0d3">Pharma & Supplies</small></span></div>
        <div class="nav-label">Management</div>
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span class="nav-icon">▦</span><span class="nav-text">Dashboard</span></a>
        <details class="nav-group" @if(request()->routeIs('products.*', 'stock-ins.*', 'adjustments.*', 'sales-orders.*')) open @endif>
            <summary class="nav-link"><span class="nav-icon">&#9638;</span><span class="nav-text">Product Management</span></summary>
            <div class="nav-submenu">
                <a class="nav-sublink {{ request()->routeIs('products.index', 'products.edit') ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a>
                <a class="nav-sublink {{ request()->routeIs('products.create') ? 'active' : '' }}" href="{{ route('products.create') }}">Add Product</a>
                <a class="nav-sublink {{ request()->routeIs('stock-ins.*') ? 'active' : '' }}" href="{{ route('stock-ins.index') }}">Stock-in Lots</a>
                <a class="nav-sublink {{ request()->routeIs('adjustments.*') ? 'active' : '' }}" href="{{ route('adjustments.index') }}">Stock Adjustments</a>
                <a class="nav-sublink {{ request()->routeIs('sales-orders.*') ? 'active' : '' }}" href="{{ route('sales-orders.index') }}">Sales Orders (SO)</a>
            </div>
        </details>
        <a class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}" href="{{ route('purchase-orders.index') }}"><span class="nav-icon">▧</span><span class="nav-text">Purchase orders</span></a>
        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><span class="nav-icon">▧</span><span class="nav-text">Categories</span></a>
        <a class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}" href="{{ route('activity-logs.index') }}"><span class="nav-icon">≋</span><span class="nav-text">Activity logs</span></a>
        @if(optional(auth()->user())->role === 'admin')
        <details class="nav-group" @if(request()->routeIs('reports.*', 'expired-stock.*')) open @endif>
            <summary class="nav-link"><span class="nav-icon">&#9638;</span><span class="nav-text">Inventory Reports</span></summary>
            <div class="nav-submenu">
                <a class="nav-sublink {{ request('type') === 'expired-items' ? 'active' : '' }}" href="{{ route('reports.index', ['type' => 'expired-items']) }}">Expired Items</a>
                <a class="nav-sublink {{ request('type') === 'low-stock' ? 'active' : '' }}" href="{{ route('reports.index', ['type' => 'low-stock']) }}">Low Stock</a>
                <a class="nav-sublink {{ request('type') === 'expiring-soon' ? 'active' : '' }}" href="{{ route('reports.index', ['type' => 'expiring-soon']) }}">Expiring Soon</a>
            </div>
        </details>
        @endif
        @if(optional(auth()->user())->role === 'admin')
        <details class="nav-group" @if(request()->routeIs('permissions.*', 'users.*')) open @endif>
            <summary class="nav-link"><span class="nav-icon">&#9881;</span><span class="nav-text">Access Control</span></summary>
            <div class="nav-submenu">
                <a class="nav-sublink {{ request()->routeIs('permissions.*') ? 'active' : '' }}" href="{{ route('permissions.index') }}">Permissions</a>
                <a class="nav-sublink {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Roles</a>
            </div>
        </details>
        @endif
        @if(optional(auth()->user())->role === 'admin')<a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><span class="nav-icon">◫</span><span class="nav-text">Suppliers</span></a>
        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><span class="nav-icon">▥</span><span class="nav-text">Reports</span></a>
        <a class="nav-link {{ request()->routeIs('expired-stock.*') ? 'active' : '' }}" href="{{ route('expired-stock.index') }}"><span class="nav-icon">!</span><span class="nav-text">Expired stock</span></a>
        <a class="nav-link {{ request()->routeIs('permission-requests.*') && !request()->routeIs('permission-requests.mine') ? 'active' : '' }}" href="{{ route('permission-requests.index') }}"><span class="nav-icon">◇</span><span class="nav-text">Access requests</span></a>@endif
        @if(optional(auth()->user())->role === 'admin')<a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><span class="nav-icon">♙</span><span class="nav-text">Users & access</span></a>@endif
        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}"><span class="nav-icon">◎</span><span class="nav-text">Profile</span></a>
    </aside>
    <main class="main">
        <header class="topbar"><span class="crumb">Operations / @yield('section', 'Overview')</span><div class="profile">@auth <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span>{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role) }}</span><form method="post" action="{{ route('logout') }}">@csrf<button class="btn" type="submit" title="Sign out">↪</button></form>@else<a class="btn" href="{{ route('login') }}">Sign in</a>@endauth</div></header>
        <div class="content">@if(session('status'))<div class="flash">{{ session('status') }}</div>@endif @yield('content')</div>
    </main>
</div>
</body>
</html>

