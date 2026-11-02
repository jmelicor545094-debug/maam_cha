<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View { return view('users.index', ['users' => User::orderBy('name')->paginate(15)]); }
    public function archived(): View { return view('users.archived', ['users' => User::onlyTrashed()->orderBy('name')->paginate(15)]); }
    public function create(): View { return view('users.form', ['user' => new User()]); }
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $data['password'] = Hash::make($data['password']); User::create($data);
        ActivityLog::record('Users', 'Created', 'Created user account '.$data['email'].'.');
        return redirect()->route('users.index')->with('status', 'User account created.');
    }
    public function edit(User $user): View { return view('users.form', compact('user')); }
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate($this->rules($user));
        if ($user->is(auth()->user()) && $data['role'] !== 'admin') return back()->withErrors(['role' => 'You cannot remove your own administrator access.'])->withInput();
        if ($user->role === 'admin' && $data['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) return back()->withErrors(['role' => 'At least one administrator account must remain.'])->withInput();
        if (blank($data['password'])) unset($data['password']); else $data['password'] = Hash::make($data['password']);
        $user->update($data);
        ActivityLog::record('Users', 'Updated', 'Updated user account '.$user->email.'.');
        return redirect()->route('users.index')->with('status', 'User account updated.');
    }
    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) return back()->withErrors(['user' => 'You cannot remove your own account.']);
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) return back()->withErrors(['user' => 'At least one administrator account must remain.']);
        $user->delete(); ActivityLog::record('Users', 'Archived', 'Archived user account '.$user->email.'.'); return redirect()->route('users.index')->with('status', 'User account archived.');
    }

    public function restore(int $userId): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        $user->restore(); ActivityLog::record('Users', 'Restored', 'Restored user account '.$user->email.'.');
        return redirect()->route('users.archived')->with('status', 'User account restored.');
    }

    private function rules(?User $user = null): array
    {
        return [
            'name' => ['required','string','max:120'], 'email' => ['required','email','max:160','unique:users,email'.($user ? ','.$user->id : '')],
            'role' => ['required','in:admin,staff'], 'password' => [$user ? 'nullable' : 'required','string','min:8','confirmed'],
            'contact_number' => ['nullable','string','max:40'], 'birthday' => ['nullable','date','before:today'], 'sex' => ['nullable','in:female,male,prefer_not_to_say'],
            'street' => ['nullable','string','max:160'], 'city' => ['nullable','string','max:100'], 'province' => ['nullable','string','max:100'], 'zip_code' => ['nullable','string','max:20'],
        ];
    }
}
