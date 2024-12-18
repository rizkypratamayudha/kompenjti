<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function loginAPI(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|string'
        ]);

        // Find user by username
        $user = UserModel::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau Password yang anda inputkan salah, periksa kembali inputan anda'
            ], 401);
        }

        // Check if role matches
        if ($user->getRoleName() !== $request->role) {
            return response()->json([
                'success' => false,
                'message' => 'Role yang anda pilih tidak cocok dengan username dan password yang anda inputkan'
            ], 403);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'user' => [
                'id' => $user->user_id,
                'name' => $user->nama,
                'username' => $user->username,
                'password' => $user->password,
                'role' => $user->getRoleName(),
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null
            ],
            'token' => $token
        ]);
    }
}