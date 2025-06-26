<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::all();
        return view('superuser.roles', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles']);
        Role::create($request->only('name'));
        return back()->with('success', 'Role created!');
    }

    public function edit(Role $role)
    {
        // Don't allow editing protected roles
        if (in_array(strtolower($role->name), Role::PROTECTED_ROLES)) {
            return back()->withErrors(['edit' => 'Cannot modify protected role!']);
        }

        return view('superuser.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        // Don't allow updating protected roles
        if (in_array(strtolower($role->name), Role::PROTECTED_ROLES)) {
            return back()->withErrors(['name' => 'Cannot modify protected role!']);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)]
        ]);

        $role->update([
            'name' => $request->name
        ]);

        return back()->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        if (in_array(strtolower($role->name), Role::PROTECTED_ROLES)) {
            abort(403, 'Protected role cannot be deleted!');
        }

        if ($role->users()->exists()) {
            return back()->withErrors([
                'role' => 'Cannot delete: Role has active users!'
            ]);
        }
        
        $role->delete();
        return back()->with('success', 'Role deleted successfully!');
    }
}
