<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables as DataTablesDataTables;
use Yajra\DataTables\Facades\DataTables;

class LevelController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title'=>'Daftar Roles',
            'list'=>['Home','Roles']
        ];

        $page = (object)[
            'title'=>'Daftar Role yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level';
        $level = LevelModel::all();
        return view('level.index',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu,'level'=>$level]);
    }

    public function list(Request $request)
    {
        $level = LevelModel::select('level_id', 'kode_level', 'level_nama',);

        if ($request->level_id){
            $level->where('level_id',$request->level_id);
        }
        return DataTables::of($level)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($level) { // menambahkan kolom aksi
                // $btn = '<a href="' . url('/level/' . $level->level_id) . '" class="btn btn-info btnsm">Detail</a> ';
                // $btn .= '<a href="' . url('/level/' . $level->level_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/level/' . $level->level_id) . '">'
                //     . csrf_field() . method_field('DELETE') .
                //     '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                // return $btn;
                $btn  = '<button onclick="modalAction(\'' . url('/level/' . $level->level_id .
                    '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/level/' . $level->level_id .
                    '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/level/' . $level->level_id .
                    '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create_ajax(){
        return view ('level.create_ajax');
    }
    public function store_ajax(Request $request){
        if($request->ajax()||$request->wantsJson()){
            $rules = [
                'level_kode'=>'required|string|unique:m_level,level_kode',
                'level_nama'=>'required|string|max:100'
            ];

            $validator = Validator::make($request->all(),$rules);
            if($validator->fails()){
                return response()->json([
                    'status'=>false,
                    'message'=>'Validasi Gagal',
                    'msgField'=>$validator->errors()
                ]);
            }

            levelmodel::create($request->all());
            return response()->json([
                'status'=>true,
                'message'=>'Data level berhasil disimpan'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(Request $request, $level_id){
        $level = LevelModel::find($level_id);

        return view ('level.edit_ajax',['level'=>$level]);
    }

    public function update_ajax (Request $request, $level_id){
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required|string|unique:m_level,level_kode,' .$level_id.',level_id',
                'level_nama'=>'required|string|max:100'

            ];
            // use Illuminate\Support\Facades\Validator;
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,    // respon json, true: berhasil, false: gagal
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()  // menunjukkan field mana yang error
                ]);
            }

            $check = levelmodel::find($level_id);
            if ($check) {
                $check->update($request->all());
                return response()->json([
                    'status'  => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax ($level_id){
        $level = LevelModel::find($level_id);

        return view('level.confirm_ajax',['level'=>$level]);
    }

    public function delete_ajax(Request $request, $level_id)
{
    if ($request->ajax() || $request->wantsJson()) {
        $level = LevelModel::find($level_id);

        if ($level) {
            try {
                $level->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }

    return redirect('/');
}

    public function show_ajax ($level_id){
        $level = LevelModel::find($level_id);

        return view('level.show_ajax',['level'=>$level]);
    }
}
