<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendingRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function registerWithDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                'unique:t_pending_register,username',
                function ($attribute, $value, $fail) {
                    if (DB::table('m_user')->where('username', $value)->exists()) {
                        $fail('The username has already been taken.');
                    }
                },
            ],
            'password' => 'required|min:5',
            'nama' => 'required|string',
            'level_id' => 'required|exists:m_level,level_id',
            'email' => [
                'required',
                'email',
                'unique:t_pending_register,email',
                function ($attribute, $value, $fail) {
                    if (DB::table('detail_mahasiswa')->where('email', $value)->exists()) {
                        $fail('The email has already been taken.');
                    }
                },
            ],
            'no_hp' => 'required|string',
            'prodi_id' => 'nullable|exists:prodi,prodi_id',
            'angkatan' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $maxUserIdInUser = DB::table('m_user')->max('user_id');
            $maxUserIdInPending = DB::table('t_pending_register')->max('user_id');
            $nextUserId = max($maxUserIdInUser, $maxUserIdInPending) + 1;

            $prodiId = $request->level_id == 3 ? $request->prodi_id : null; // Set ke null jika bukan mahasiswa
            $angkatan = $request->level_id == 3 ? $request->angkatan : null; // Set ke null jika bukan mahasiswa

            PendingRegister::create([
                'user_id' => $nextUserId,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'nama' => $request->nama,
                'level_id' => $request->level_id,
                'prodi_id' => $prodiId,
                'angkatan' => $angkatan,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json(['message' => 'Registration successful'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Registration failed. Please try again.'], 500);
        }
    }
}