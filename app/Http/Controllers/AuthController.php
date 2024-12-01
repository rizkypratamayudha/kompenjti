<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) { // jika sudah login, maka redirect ke halaman home
            return redirect('/');
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $credentials = $request->only('username', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                $redirectUrl = match ($user->level->kode_level) {
                    'ADM' => url('/'),
                    'MHS' => url('/dashboardMhs'),
                    'DSN' => url('/dashboardDos'),
                    'KPD' => url('/dashboardKap')
                };

                return response()->json([
                    'status' => true,
                    'message' => 'Login Berhasil',
                    'redirect' => $redirectUrl,
                ]);
            }

            Log::error('Login failed for user: ' . $request->username);
            return response()->json([
                'status' => false,
                'message' => 'Login Gagal',
            ]);
        }

        return redirect('login');
    }



    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
}
