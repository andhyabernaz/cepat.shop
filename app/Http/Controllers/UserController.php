<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAddress;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

   public function userList(Request $request)
   {
      $data =  DB::table('users')->select(
         'users.id',
         'users.role_id',
         'users.name',
         'users.email',
         'users.phone',
         'users.saldo_balance',
         'users.affiliate_saldo',
         'roles.name as role_name',
      )
         ->when($request->search, function ($q) use ($request) {
            $key = $request->search;

            $q->where('users.name', 'like', '%' . $key . '%')
               ->orWhere('users.email', 'like', '%' . $key . '%')
               ->orWhere('users.phone', 'like', '%' . $key . '%');
         })
         ->leftJoin('roles', 'roles.id', 'users.role_id')
         ->orderByRaw('role_id IS NULL ASC, role_id ASC')
         ->paginate($request->per_page ?? 10)
         ->withQueryString();
      return ApiResponse::success($data);
   }

   public function index(Request $request)
   {
      $data = User::when($request->search, function ($q) use ($request) {
         $key = $request->search;

         $q->where('name', 'like', '%' . $key . '%')
            ->orWhere('email', 'like', '%' . $key . '%')
            ->orWhere('phone', 'like', '%' . $key . '%');
      })->paginate($request->per_page ?? 10)->withQueryString();
      return ApiResponse::success($data);
   }

   public function store(Request $request)
   {
      $request->validate([
         'name' => ['required', 'string', 'max:60'],
         'phone' => ['required', 'string', 'max:20', 'unique:users'],
         'email' => ['required', 'string', 'email', 'max:80', 'unique:users'],
         'role_id' => 'nullable',
         'password' => ['required', 'confirmed'],
      ], [
         'name.required' => 'Nama wajib diisi.',
         'phone.required' => 'Nomor ponsel wajib diisi.',
         'phone.unique' => 'Nomor ponsel sudah terdaftar.',
         'email.unique' => 'Email sudah terdaftar.',
         'password.confirmed' => 'Password konfirmasi tidak sama.',
      ]);

      $user = User::create([
         'name' => $request->name,
         'email' => $request->email,
         'phone' => $request->phone,
         'role_id' => $request->role_id ?? NULL,
         'password' => Hash::make($request->password),
      ]);

      return ApiResponse::success($user);
   }

   public function update(Request $request, $id)
   {
      $user = User::findOrFail($id);

      $request->validate([
         'name' => 'required',
         'phone' => 'required|unique:users,phone,' . $user->id,
         'email' => 'required|email|unique:users,email,' . $user->id,
         'role_id' => 'nullable',
         'password' => 'nullable|confirmed'
      ], [
         'name.required' => 'Nama wajib diisi.',
         'phone.required' => 'Nomor ponsel wajib diisi.',
         'email.required' => 'Email wajib diisi.',
         'phone.unique' => 'Nomor ponsel sudah terdaftar.',
         'email.unique' => 'Email sudah terdaftar.',
         'password.confirmed' => 'Password konfirmasi salah.',
      ]);


      $user->name = $request->name;
      $user->email = $request->email;
      $user->phone = $request->phone;
      $user->role_id = $request->role_id ?? NULL;

      if ($request->password) {
         $user->password = Hash::make($request->password);
      }
      $user->save();

      $data = $user->load('address');
      return ApiResponse::success($data);
   }

   public function destroy($id)
   {
      $user = User::findOrFail($id);

      if (env('FORCE_USER_DELETE') == true) {
         $user->forceDelete();
      } else {
         $user->delete();
      }

      UserAddress::where('user_id', $user->id)->delete();

      return ApiResponse::success();
   }
}
