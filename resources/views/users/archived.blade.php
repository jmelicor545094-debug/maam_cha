@extends('layouts.app')
@section('title', 'Archived users')
@section('section', 'Users & access')
@section('content')
<div class="page-heading"><div><div class="eyebrow">Access control</div><h1>Archived user accounts</h1><div class="muted">Restore an account to allow the user to sign in again.</div></div><a class="btn" href="{{ route('users.index') }}">Active users</a></div>
<section class="panel"><div class="panel-head"><h2>Archived accounts</h2></div><div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Archived</th><th></th></tr></thead><tbody>@forelse($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ ucfirst($user->role) }}</td><td>{{ $user->deleted_at->format('M d, Y') }}</td><td><form method="post" action="{{ route('users.restore',$user->id) }}">@csrf<button class="btn primary">Restore</button></form></td></tr>@empty<tr><td colspan="5" style="padding:24px;text-align:center;color:#71848b">No archived accounts.</td></tr>@endforelse</tbody></table></div><div class="pagination"><span>{{ $users->total() }} archived accounts</span>{{ $users->links('pagination::simple-tailwind') }}</div></section>
@endsection
