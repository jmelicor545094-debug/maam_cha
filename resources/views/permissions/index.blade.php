@extends('layouts.app')
@section('title', 'Permissions')
@section('section', 'Permissions')
@section('content')
<style>
    .permission-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:18px}
    .permission-heading h1{font-size:23px;margin:0 0 5px;color:#243a42}
    .permission-crumb{font-size:11px;color:#819196}
    .permission-add,.permission-edit{position:relative}
    .permission-add summary,.permission-edit summary{list-style:none;cursor:pointer}
    .permission-add summary::-webkit-details-marker,.permission-edit summary::-webkit-details-marker{display:none}
    .permission-add-popover,.permission-edit-popover{position:absolute;right:0;top:42px;z-index:10;width:min(320px,calc(100vw - 36px));padding:15px;background:#fff;border:1px solid #dfe8e7;border-radius:6px;box-shadow:0 12px 32px #183b4024}
    .permission-edit-popover{top:37px;width:min(270px,calc(100vw - 40px));padding:12px}
    .permission-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:12px 16px;border-bottom:1px solid #e9eeee}
    .permission-length,.permission-search{display:flex;align-items:center;gap:8px;font-size:11px;color:#657980}
    .permission-length select{width:auto;min-width:66px;padding:7px 8px}
    .permission-search input{width:190px;padding:8px 10px}
    .permission-search .btn{min-height:34px;padding:7px 10px}
    .permission-table th,.permission-table td{padding:12px 16px}
    .permission-table th{background:#f7f9f9}
    .permission-actions{display:flex;justify-content:flex-end;align-items:center;gap:7px}
    .permission-edit .icon-btn{width:32px;height:32px;padding:0;border:0;border-radius:4px;background:#16796b;color:white}
    .permission-edit .field{margin:0 0 10px}
    .permission-footer{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 16px;color:#71848b;font-size:11px}
    .permission-footer nav{display:flex;align-items:center;gap:4px}
    .permission-footer nav a,.permission-footer nav span{padding:6px 9px;border:1px solid #e0e7e8;border-radius:3px}
    .permission-footer nav [aria-current=page]{background:#16796b;color:#fff;border-color:#16796b}
    .permission-footer nav .disabled{color:#a5b0b3;background:#f7f9f9}
    @media(max-width:600px){.permission-heading{align-items:center}.permission-toolbar{align-items:stretch;flex-direction:column}.permission-search{width:100%}.permission-search input{width:100%}.permission-footer{align-items:flex-start;flex-direction:column}}
</style>
<div class="permission-heading"><div><h1>Permissions</h1><div class="permission-crumb">Dashboard / Permissions</div></div><details class="permission-add"><summary class="btn primary">Add Permission</summary><form class="permission-add-popover" method="post" action="{{ route('permissions.store') }}">@csrf<h2 style="font-size:14px;margin:0 0 12px">New permission</h2><div class="field"><label for="permission-name">Permission name</label><input id="permission-name" name="name" value="{{ old('name') }}" placeholder="create-product" required maxlength="100" pattern="[a-z][a-z0-9-]*"><span class="muted">Use lowercase letters, numbers, and hyphens.</span></div><button class="btn primary" type="submit">Save permission</button></form></details></div>
@if($errors->any())<div class="flash" style="background:#fce8e7;color:#a6423c;border-color:#f3d3d0">{{ $errors->first() }}</div>@endif
<section class="panel">
    <div class="panel-head"><h2>Permission register <span class="muted">({{ $permissions->total() }})</span></h2></div>
    <form class="permission-toolbar" method="get" action="{{ route('permissions.index') }}">
        <label class="permission-length">Show <select name="per_page" onchange="this.form.submit()"><option value="10" @selected($perPage === 10)>10</option><option value="25" @selected($perPage === 25)>25</option><option value="50" @selected($perPage === 50)>50</option></select> entries</label>
        <label class="permission-search"><span>Search:</span><input name="search" value="{{ request('search') }}" aria-label="Search permissions" placeholder="Permission name"><button class="btn" type="submit">Search</button></label>
    </form>
    <div class="table-wrap"><table class="permission-table"><thead><tr><th>Name</th><th>Created date</th><th style="text-align:right">Actions</th></tr></thead><tbody>
        @forelse($permissions as $permission)
            <tr><td><strong>{{ $permission->name }}</strong></td><td>{{ $permission->created_at?->format('D, d M Y') }}</td><td><div class="permission-actions">
                <details class="permission-edit"><summary class="btn icon-btn" title="Edit permission" aria-label="Edit {{ $permission->name }}">&#9998;</summary><form class="permission-edit-popover" method="post" action="{{ route('permissions.update', $permission) }}">@csrf @method('PUT')<div class="field"><label>Permission name</label><input name="name" value="{{ $permission->name }}" required maxlength="100" pattern="[a-z][a-z0-9-]*"></div><button class="btn primary" type="submit">Save changes</button></form></details>
                <form method="post" action="{{ route('permissions.destroy', $permission) }}" onsubmit="return confirm('Delete this permission?')">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete</button></form>
            </div></td></tr>
        @empty<tr><td colspan="3" style="padding:28px;text-align:center;color:#71848b">No permissions match your search.</td></tr>@endforelse
    </tbody></table></div>
    <div class="permission-footer"><span>Showing {{ $permissions->firstItem() ?? 0 }} to {{ $permissions->lastItem() ?? 0 }} of {{ $permissions->total() }} entries</span>
        @php($firstPage = max(1, $permissions->currentPage() - 2))
        @php($lastPage = min($permissions->lastPage(), $permissions->currentPage() + 2))
        <nav aria-label="Permission pages">
            @if($permissions->onFirstPage())<span class="disabled">Previous</span>@else<a href="{{ $permissions->previousPageUrl() }}">Previous</a>@endif
            @if($firstPage > 1)<a href="{{ $permissions->url(1) }}">1</a>@if($firstPage > 2)<span class="disabled">...</span>@endif @endif
            @for($page = $firstPage; $page <= $lastPage; $page++)@if($page === $permissions->currentPage())<span aria-current="page">{{ $page }}</span>@else<a href="{{ $permissions->url($page) }}">{{ $page }}</a>@endif @endfor
            @if($lastPage < $permissions->lastPage())@if($lastPage < $permissions->lastPage() - 1)<span class="disabled">...</span>@endif<a href="{{ $permissions->url($permissions->lastPage()) }}">{{ $permissions->lastPage() }}</a>@endif
            @if($permissions->hasMorePages())<a href="{{ $permissions->nextPageUrl() }}">Next</a>@else<span class="disabled">Next</span>@endif
        </nav>
    </div>
</section>
@endsection
