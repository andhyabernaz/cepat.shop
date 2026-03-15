<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PermissionController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'role_id' => 'required',
            'permission_id' => 'required',
        ]);

        $role = Role::findOrFail($request->role_id);

        $role->permissions()->toggle($request->permission_id);

        return ApiResponse::success();
    }


    public function rolePermissions()
    {

         $roles = Role::with('permissions')->get()->map(function ($el) {
            return [
               'id' => $el->id,
               'name' => $el->name,
               'sort' => $el->sort,
               'permissions' => $el->permissions->pluck('id')
            ];
         });
        $data = [
            'modules' => Permission::MODULES,
            'roles' => $roles,
            'permissions' => Cache::remember('all-permissions', 700, function() {
                return Permission::all();
            })
        ];

        return ApiResponse::success($data);
    }

    public function getUserPermissions()
    {
        $data = User::getUserPermissions();
        return ApiResponse::success($data);
    }

}
