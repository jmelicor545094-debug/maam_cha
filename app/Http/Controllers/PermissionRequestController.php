<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PermissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Rule;
use Illuminate\View\View;

class PermissionRequestController extends Controller
{
    public function mine(): View { return view('permission-requests.mine', ['requests' => PermissionRequest::where('user_id', auth()->id())->latest()->paginate(15)]); }
    public function index(): View { return view('permission-requests.index', ['requests' => PermissionRequest::with('user')->latest()->paginate(20)]); }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['request_type' => ['required','in:profile,email'], 'new_email' => ['nullable','required_if:request_type,email','email','max:160',Rule::unique('users','email')], 'name' => ['nullable','string','max:120'], 'contact_number' => ['nullable','string','max:40'], 'birthday' => ['nullable','date','before:today'], 'sex' => ['nullable','in:female,male,prefer_not_to_say'], 'street' => ['nullable','string','max:160'], 'city' => ['nullable','string','max:100'], 'province' => ['nullable','string','max:100'], 'zip_code' => ['nullable','string','max:20']]);
        $payload = $data['request_type'] === 'email' ? ['email' => $data['new_email']] : ['profile' => collect($data)->only(['name','contact_number','birthday','sex','street','city','province','zip_code'])->all()];
        PermissionRequest::create(['user_id' => auth()->id(), 'request_type' => $data['request_type'], 'requested_data' => $payload, 'status' => 'pending']);
        ActivityLog::record('Access Control', 'Requested Change', 'Submitted a '.$data['request_type'].' change request.');
        return back()->with('status', 'Request submitted for administrator review.');
    }

    public function approve(Request $request, PermissionRequest $permissionRequest): RedirectResponse
    {
        if ($permissionRequest->status !== 'pending') return back()->withErrors(['permission_request' => 'This request has already been reviewed.']);
        $notes = $request->validate(['admin_notes' => ['nullable','string','max:1000']]);
        $payload = $permissionRequest->requested_data;
        $user = $permissionRequest->user;
        if (! empty($payload['email'])) {
            validator(['email' => $payload['email']], ['email' => ['required','email','max:160',Rule::unique('users','email')->ignore($user->id)]])->validate();
            $user->email = $payload['email'];
        }
        if (! empty($payload['profile'])) $user->fill($payload['profile']);
        $user->save();
        $permissionRequest->update(['status' => 'approved', 'reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'admin_notes' => $notes['admin_notes'] ?? null]);
        ActivityLog::record('Access Control', 'Approved Request', 'Approved a '.$permissionRequest->request_type.' request for '.$user->name.'.');
        return back()->with('status', 'Request approved and account details updated.');
    }

    public function reject(Request $request, PermissionRequest $permissionRequest): RedirectResponse
    {
        if ($permissionRequest->status !== 'pending') return back()->withErrors(['permission_request' => 'This request has already been reviewed.']);
        $data = $request->validate(['admin_notes' => ['nullable','string','max:1000']]);
        $permissionRequest->update(['status' => 'rejected', 'reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'admin_notes' => $data['admin_notes'] ?? null]);
        ActivityLog::record('Access Control', 'Rejected Request', 'Rejected a '.$permissionRequest->request_type.' request for '.$permissionRequest->user->name.'.');
        return back()->with('status', 'Request rejected.');
    }
}
