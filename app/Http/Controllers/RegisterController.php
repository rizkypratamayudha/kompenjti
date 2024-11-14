<?php

namespace App\Http\Controllers;

use App\Models\DetailMhsModel;
use App\Models\LevelModel;
use App\Models\PendingRegister;
use App\Models\PeriodeModel;
use App\Models\ProdiModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register()
    {
        $level = LevelModel::whereIn('level_id', [2, 3])->get();
        $user = UserModel::all();
        $prodi = ProdiModel::all();
        $periode = PeriodeModel::all();
        return view('register.register', ['level' => $level, 'user' => $user, 'prodi' => $prodi,'periode'=> $periode]);
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
        return redirect('/');
    }


}
