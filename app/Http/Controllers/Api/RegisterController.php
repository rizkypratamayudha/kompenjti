<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LevelModel;
use App\Models\PendingRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    public function index(){
        return LevelModel::all();
    }
    public function store(Request $request)
    {
        $rules = [
            'level_id' => 'required|integer',
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama' => 'required|string|max:100',
            'password' => 'required|min:6',
            'email' => 'required|email',
            'no_hp' => 'required|string',
            'prodi_id' => 'required_if:level_id,3',
            'angkatan' => 'required_if:level_id,3'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        PendingRegister::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Data user berhasil disimpan',
            'redirect' => url('login')
        ], 201);
    }
}
