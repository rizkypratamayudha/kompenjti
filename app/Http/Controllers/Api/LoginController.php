<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function loginAPI(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|string'
        ]);

        // Cari user berdasarkan username
        $user = UserModel::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau Password yang anda inputkan salah, periksa kembali inputan anda'
            ], 401);
        }

        // Cek apakah role sesuai
        if ($user->getRoleName() !== $request->role) {
            return response()->json([
                'success' => false,
                'message' => 'Role yang anda pilih tidak cocok dengan username dan password yang anda inputkan'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'user' => [
                'id' => $user->user_id,
                'name' => $user->nama,
                'role' => $user->getRoleName()
            ]
        ]);
    }
}
