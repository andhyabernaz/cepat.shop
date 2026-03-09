<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $data = Role::when($request->with, function ($q) {
            $q->with('permissions');
        })
            ->get();

        return ApiResponse::success($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'unique:roles,name']
        ]);

        $data = Role::create([
            'name' => $request->name
        ]);
        return ApiResponse::success($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'unique:roles,name,' . $id]
        ]);

        $data = tap(Role::find($id), function ($role) use ($request) {
            $role->update(['name' => $request->name]);
        });
        return ApiResponse::success($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        DB::table('permission_role')->where('role_id', $id)->delete();
        $role->delete();
    }

    public function assign(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'role_id' => 'nullable',
        ]);

        $user = User::findOrFail($request->user_id);

        $user->role_id = $request->role_id;

        $user->save();

        Cache::flush();

        return ApiResponse::success($user);
    }
}
