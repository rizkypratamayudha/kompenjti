<?php

namespace App\Http\Controllers;

use App\Models\PekerjaanModel;
use App\Models\PendingPekerjaanController;
use App\Models\PendingPekerjaanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ListPekerjaanMHSController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title' => 'List Pekerjaan',
            'list'=> ['Home','List Pekerjaan']
        ];

        $page = (object)[
            'title' => 'List Pekerjaan yang tersedia'
        ];

        $activeMenu = 'pekerjaan';
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan','progres')->where('status','open')->get();
        return  view('pekerjaanMHS.index',['breadcrumb' => $breadcrumb,'page'=> $page,'activeMenu'=> $activeMenu,'tugas' => $pekerjaan]);
    }

    public function apply(Request $request){
        $validator = Validator::make($request->all(),[
            'pekerjaan_id' => 'required|exists:pekerjaan,pekerjaan_id',
            'user_id' => 'required|exists:m_user,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        PendingPekerjaanModel::create([
            'pekerjaan_id' => $request->pekerjaan_id,
            'user_id' => $request->user_id,
        ]);

        return back()->with('success','Anda Telah Berhasil Melamar Pekerjaan');
    }
}
