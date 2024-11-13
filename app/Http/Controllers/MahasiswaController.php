<?php

namespace App\Http\Controllers;

use App\Models\jamKompenModel;
use App\Models\SemesterModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MahasiswaController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title'=>'Data Mahasiswa Kompensasi',
            'list'=>['Home','Jam Kompen']
        ];

        $page = (object)[
            'title'=>'Daftar Mahasiswa Kompensasi yang terdaftar dalam sistem'
        ];

        $activeMenu = 'mhs';
        $user = UserModel::all();
        $semester = SemesterModel::all();
        return view('mahasiswa.index',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu,'user'=>$user,'semester'=>$semester]);
    }

    public function list(Request $request)
    {
        $jamKompen = jamKompenModel::select('jam_kompen_id', 'akumulasi_jam', 'user_id', 'semester_id') 
        -> with('user')
        -> with('semester');

        if ($request->semester_id){
            $jamKompen->where('semester_id',$request->semester_id);
        }
        return DataTables::of($jamKompen)
            ->addIndexColumn()
            ->addColumn('aksi', function ($jamKompen) { 
                $btn  = '<button onclick="modalAction(\'' . url('/mahasiswa/' . $jamKompen->jam_kompen_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/mahasiswa/' . $jamKompen->jam_kompen_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/mahasiswa/' . $jamKompen->jam_kompen_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) 
            ->make(true);
    }
}
