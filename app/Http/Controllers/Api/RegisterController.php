<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\detail_mahasiswaModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function registerWithDetails(Request $request)
    {
        // Validate data for both tables
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:m_user,username',
            'password' => 'required|min:5',
            'nama' => 'required|string',
            'level_id' => 'required|exists:m_level,level_id',
            'prodi' => 'required|exists:prodi,prodi_id',
            'email' => 'required|email|unique:b_mahasiswa,email',
            'hp' => 'required|string',
            'angkatan' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Use transactions to ensure both inserts succeed
        try {
            DB::beginTransaction();

            // Step 1: Create user data in `m_user` table
            $user = UserModel::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'nama' => $request->nama,
                'level_id' => $request->level_id,
            ]);

            // Step 2: Create mahasiswa data in `b_mahasiswa` table
            detail_mahasiswaModel::create([
                'user_id' => $user->user_id,
                'prodi_id' => $request->prodi_id,
                'email' => $request->email,
                'hp' => $request->hp,
                'angkatan' => $request->angkatan,
            ]);

            DB::commit();

            return response()->json(['message' => 'Registration successful'], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Registration failed. Please try again.'], 500);
        }
    }
}
