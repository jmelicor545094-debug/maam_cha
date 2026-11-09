<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PermissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit', ['profile' => auth()->user(), 'requests' => PermissionRequest::where('user_id', auth()->id())->latest()->take(10)->get()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $data = $request->validate([
            'name' => ['required','string','max:120'], 'email' => ['required','email','max:160', Rule::unique('users', 'email')->ignore($user->id)],
            'contact_number' => ['nullable','string','max:40'], 'birthday' => ['nullable','date','before:today'], 'sex' => ['nullable','in:female,male,prefer_not_to_say'],
            'street' => ['nullable','string','max:160'], 'city' => ['nullable','string','max:100'], 'province' => ['nullable','string','max:100'], 'zip_code' => ['nullable','string','max:20'],
        ]);
        if ($user->role === 'admin') {
            $user->update($data); ActivityLog::record('Profile', 'Updated', 'Updated own profile details.');
            return back()->with('status', 'Profile updated.');
        }
        $profileData = collect($data)->except('email')->all();
        $changedProfile = collect($profileData)->some(fn ($value, $key) => (string) ($user->$key ?? '') !== (string) ($value ?? ''));
        $changedEmail = $data['email'] !== $user->email;
        if (! $changedProfile && ! $changedEmail) return back()->with('status', 'Your profile already has those details.');
        PermissionRequest::create(['user_id' => $user->id, 'request_type' => $changedProfile && $changedEmail ? 'profile_and_email' : ($changedEmail ? 'email' : 'profile'), 'requested_data' => ['profile' => $profileData, 'email' => $changedEmail ? $data['email'] : null], 'status' => 'pending']);
        ActivityLog::record('Profile', 'Requested Change', 'Submitted a profile change request for administrator review.');
        return back()->with('status', 'Profile change request submitted for approval.');
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validate(['current_password' => ['required','current_password'], 'password' => ['required','string','min:8','confirmed']]);
        auth()->user()->update(['password' => Hash::make($data['password'])]);
        ActivityLog::record('Profile', 'Password Changed', 'Changed account password.');
        return back()->with('status', 'Password changed.');
    }

    public function deactivate(Request $request): RedirectResponse
    {
        $request->validate(['current_password' => ['required','current_password']]);
        $user = auth()->user();
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) return back()->withErrors(['deactivate' => 'You cannot deactivate the last administrator account.']);
        ActivityLog::record('Profile', 'Account Deactivated', 'Deactivated own account.');
        $user->delete();
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'Account deactivated. An administrator can restore it if needed.');
    }
}
