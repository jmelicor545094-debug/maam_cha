<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Rule;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50], true) ? (int) $request->input('per_page', 10) : 10;
        $permissions = Permission::query()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->input('search').'%'))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('permissions.index', compact('permissions', 'perPage'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9-]*$/', 'unique:permissions,name']]);
        $permission = Permission::create($data);
        ActivityLog::record('Access Control', 'Permission Created', 'Created permission '.$permission->name.'.');

        return back()->with('status', 'Permission created.');
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9-]*$/', Rule::unique('permissions', 'name')->ignore($permission->id)]]);
        $permission->update($data);
        ActivityLog::record('Access Control', 'Permission Updated', 'Updated permission '.$permission->name.'.');

        return back()->with('status', 'Permission updated.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $name = $permission->name;
        $permission->delete();
        ActivityLog::record('Access Control', 'Permission Deleted', 'Deleted permission '.$name.'.');

        return back()->with('status', 'Permission deleted.');
    }
}
