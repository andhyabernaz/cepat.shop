<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('address');
        return ApiResponse::success($user);
    }
    
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:users,phone,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
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

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $data = $user->load('address');
        return ApiResponse::success($data);
    }
}
