<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // Show all roles
    public function index()
    {
        $roles = SpatieRole::with('permissions')->get(); // Get all roles with their permissions
        return Inertia::render('Roles/Index', [
            'roles' => $roles,
        ]);
    }

    // Show the form to create a new role
    public function create()
    {
        $permissions = Permission::all(); // Get all permissions
        return Inertia::render('Roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    // Store a new role
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = SpatieRole::create(['name' => $request->name]);

        if ($request->permissions) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    // Edit a specific role
    public function edit(SpatieRole $role)
    {
        $permissions = Permission::all();
        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    // Update a specific role
    public function update(Request $request, SpatieRole $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->name = $request->name;
        $role->save();

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    // Delete a specific role
    public function destroy(SpatieRole $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
