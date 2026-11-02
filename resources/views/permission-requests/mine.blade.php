@extends('layouts.app')
@section('title', 'My access requests')
@section('section', 'My access requests')
@section('content')
<div class="page-heading"><div><div class="eyebrow">Account settings</div><h1>My change requests</h1><div class="muted">Check the review status of profile and email changes.</div></div><a class="btn" href="{{ route('profile.edit') }}">← Account settings</a></div>
<section class="panel"><div class="panel-head"><h2>Request history</h2></div><div class="table-wrap"><table><thead><tr><th>Submitted</th><th>Request type</th><th>Requested details</th><th>Status</th><th>Admin note</th></tr></thead><tbody>@forelse($requests as $item)<tr><td>{{ $item->created_at->format('M d, Y H:i') }}</td><td>{{ ucfirst(str_replace('_',' ',$item->request_type)) }}</td><td><code>{{ json_encode($item->requested_data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</code></td><td><span class="badge {{ $item->status === 'approved' ? 'green' : ($item->status === 'rejected' ? 'red' : '') }}">{{ ucfirst($item->status) }}</span></td><td>{{ $item->admin_notes ?: '—' }}</td></tr>@empty<tr><td colspan="5" style="padding:24px;text-align:center;color:#71848b">No requests yet.</td></tr>@endforelse</tbody></table></div><div class="pagination"><span>{{ $requests->total() }} requests</span>{{ $requests->links('pagination::simple-tailwind') }}</div></section>
@endsection
