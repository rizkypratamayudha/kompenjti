<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\PendingRegister;
use App\Models\PeriodeModel;
use App\Models\ProdiModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Redirect based on user's level
            $redirectUrl = match ($user->level->kode_level) {
                'ADM' => '/',
                'MHS' => '/dashboardMhs',
                'DSN' => '/dashboardDos',
                'KPD' => '/dashboardKap',
                default => '/',
            };

            return redirect($redirectUrl);
        }

        return view('auth.auth');
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

        return redirect('auth.auth');
    }


    public function register()
    {
        $level = LevelModel::whereIn('level_id', [2, 3])->get();
        if ($level->isEmpty()) {
            return redirect()->back()->with('error', 'Data level tidak ditemukan.');
        }
    
        $user = UserModel::all();
        $prodi = ProdiModel::all();
        $periode = PeriodeModel::all();
    
        return view('auth.auth', [
            'level' => $level,
            'user' => $user,
            'prodi' => $prodi,
            'periode' => $periode,
        ]);
    }
    

    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|string|min:3|unique:m_user,username',
                'nama' => 'required|string|max:100',
                'password' => 'required|min:6|confirmed',
                'email' => 'required|email',
                'no_hp' => 'required|string',
                'prodi_id' => 'required_if:level_id,3,4',
                'angkatan' => 'required_if:level_id,3',
                'periode_id' => 'required|integer',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            $data = $request->all();
            $data['password'] = Hash::make($request->password);

            PendingRegister::create($data);
            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil disimpan',
                'redirect' => url('login')
            ]);
        }
        return redirect('auth.auth');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('register');
    }
}
