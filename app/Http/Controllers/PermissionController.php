<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Tambahkan ini

class PermissionController extends Controller
{

    // --- Fungsionalitas Baru: Daftar Izin ---
    public function index()
    {
        $permissions = Permission::withCount('roles')->get();
        return view('superuser.permissions.index', compact('permissions')); // Sesuaikan nama view
    }

    // --- Fungsionalitas Baru: Membuat Izin Baru ---
    public function create()
    {
        return view('superuser.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => ['required', 'string', 'max:255', Rule::unique('permissions')],
            'description' => 'nullable|string',
        ]);

        Permission::create($request->only('slug', 'description'));

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully!');
    }

    // --- Fungsionalitas Baru: Mengedit Izin ---
    public function edit(Permission $permission)
    {
        return view('superuser.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            // Slug harus unik, tapi abaikan slug dari permission yang sedang diedit
            'slug' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'description' => 'nullable|string',
        ]);

        $permission->update($request->only('slug', 'description'));

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully!');
    }

    // --- Fungsionalitas Baru: Menghapus Izin ---
    public function destroy(Permission $permission)
    {
        // Protect critical permissions
        $protected = ['superuser.access', 'head.access', 'staff.access'];
        if (in_array($permission->slug, $protected)) {
            abort(403, 'Core permissions cannot be deleted!');
        }

        // Check if used by roles
        if ($permission->roles()->exists()) {
            return back()->withErrors([
                'permission' => 'Cannot delete: Permission is assigned to roles!'
            ]);
        }

        $permission->delete();
        return back()->with('success', 'Permission deleted successfully!');
    }
    
    // --- Fungsionalitas yang Sudah Ada, diganti nama ---
    public function assignRolePermissions(Role $role) // Ganti nama dari 'manage'
    {
        $permissions = Permission::all();
        return view('superuser.roles.permissions', compact('role', 'permissions')); // Perbarui nama view
    }

    public function updateRolePermissions(Request $request, Role $role) // Ganti nama dari 'update'
    {
        $validated = $request->validate([
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'integer|exists:permissions,id'
        ]);

        $role->permissions()->sync($validated['permission_ids'] ?? []);
        return back()->with('success', 'Permissions updated!');
    }
}