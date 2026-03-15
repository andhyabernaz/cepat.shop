<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Jobs\DispatchNotificationJob;
use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ], [
            'email.required' => 'Email atau No ponsel wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $request->email)->orWhere('phone', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Data kredensial salah.'],
            ]);
        }

        DB::table('personal_access_tokens')->where('last_used_at', '<', now()->subMonths(3))->delete();

        $token = $user->createToken($request->device_name)->plainTextToken;

        $data = [
            'token' => $token,
            'user' => $user->load('address'),
        ];

        return ApiResponse::success($data);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:80', 'unique:users'],
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
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken($request->device_name)->plainTextToken;
        $event = NotificationTemplate::USER_REGISTRATION;

        DispatchNotificationJob::dispatch($event, $user);

        $data = [
            'token' => $token,
            'user' => $user->load('address'),
        ];

        return ApiResponse::withEvent($event)->success($data);
    }

    public function logout()
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $user->currentAccessToken()->delete();
        }

        return ApiResponse::success();
    }

    public function validationToken()
    {
        $user = auth('sanctum')->user();

        $is_valid = false;

        if ($user) {
            $is_valid = true;
            $user->load('address');
        }

        return ApiResponse::success([
            'is_valid' => $is_valid,
            'user' => $user,
        ]);
    }
}
