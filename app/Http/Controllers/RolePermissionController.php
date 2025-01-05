<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomRole;
use App\Models\Media;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function role_create()
    {
        $roles = CustomRole::get();
        return view('admin.role', compact('roles'));
    }
    public function role_store(Request $request)
    {
        $data = $request->validate([
            'role' => 'required|unique:roles,name',
            'image' => 'nullable|image|max:1024',
        ]);
        $res = CustomRole::create(['name' => $data['role']]);
        Media::upload_media($res, $data['image'], 'role_img', 'role_img');
        if ($res) {
            toastr()->success('Role Created Successfully');
        } else {
            toastr()->error('Something Went Wrong');
        }
        return redirect()->route('admin.role-create');
    }

    public function permission_create($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::get();
        $permissionsWithStatus = $permissions->map(function ($permission) use ($role) {
            return [
                'permission' => $permission->name,
                'has_permission' => $role->hasPermissionTo($permission->name),
            ];
        });
        return view('admin.permission', compact('permissionsWithStatus', 'role'));
    }
    public function permission_update(Request $req, $role)
    {
        $data = $req->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'required|exists:permissions,name',
        ]);
        $role = Role::findOrFail($role);
        $res = $role->syncPermissions($data['permissions']);
        ($res) ? toastr()->success('Permiossion Updated') : toastr()->error('Permission not updated');
        return redirect()->route('admin.role-create');
    }
}
