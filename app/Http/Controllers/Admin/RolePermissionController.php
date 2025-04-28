<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    public function createRole(Request $request)
{
    // Validate the request with custom error messages
    $request->validate([
        'name' => 'required|unique:roles,name|max:255',
    ], [
        'name.required' => 'The role name is required.',
        'name.unique' => 'The role name already exists. Please choose another name.',
        'name.max' => 'The role name may not be greater than 255 characters.',
    ]);

    // Create the role if validation passes
    $role = Role::create(['name' => $request->name]);

    return response()->json([
        'message' => 'Role created successfully.',
        'role' => $role,
    ]);
}


    public function createPermission(Request $request)
    {
        $request->validate(['name' => 'required|unique:permissions']);
        $perm = Permission::create(['name' => $request->name]);
        return response()->json(['message' => 'Permission created', 'permission' => $perm]);
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name',
        ]);
        $user = User::find($request->user_id);
        // dd(vars: $user->assignRole($request->role));

        $user->assignRole($request->role);
        return response()->json(['message' => 'Role assigned']);
    }

    public function assignPermission(Request $request)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
            'permission' => 'required|exists:permissions,name',
        ]);
        $role = Role::findByName($request->role);
        $role->givePermissionTo($request->permission);
        return response()->json(['message' => 'Permission assigned to role']);
    }
}
