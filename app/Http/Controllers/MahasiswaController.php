<?php

namespace App\Http\Controllers;

use App\Models\detail_jamKompenModel;
use App\Models\jamKompenModel;
use App\Models\MatkulModel;
use App\Models\PeriodeModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $periode = PeriodeModel::all();
        return view('mahasiswa.index',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu,'user'=>$user,'periode'=>$periode]);
    }

    public function list(Request $request)
    {
        $jamKompen = jamKompenModel::select('jam_kompen_id', 'akumulasi_jam', 'user_id', 'periode_id') 
        -> with('user')
        -> with('periode');

        if ($request->periode_id){
            $jamKompen->where('periode_id',$request->periode_id);
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
    public function create_ajax()
    {
        // Fetch data for periode and user dropdowns
        $periode = PeriodeModel::select('periode_id', 'periode_nama')->get();
        $user = UserModel::select('user_id', 'nama', 'username')->where('level_id', 3)->get();
        $detailJamKompen = detail_jamKompenModel::all();
        $matkul = MatkulModel::all(); 
    
        // Send data to the view for mahasiswa creation form
        return view('mahasiswa.create_ajax')
            ->with('periode', $periode)
            ->with('user', $user)
            ->with('detailJamKompen', $detailJamKompen)
            ->with('matkul', $matkul)
            ;
    }
    
    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'user_id' => 'required|integer',
                'periode_id' => 'required|integer',
                'jumlah_jam.*' => 'required|integer',
                'matkul_id.*' => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            // Hitung akumulasi jam dari jumlah_jam[]
            $totalJam = array_sum($request->jumlah_jam);

            $jamKompen = jamKompenModel::create([
                'user_id' => $request->user_id,
                'periode_id' => $request->periode_id,
                'akumulasi_jam' => $totalJam,
            ]);

            foreach ($request->matkul_id as $index => $matkulId) {
                detail_jamKompenModel::create([
                    'jam_kompen_id' => $jamKompen->jam_kompen_id,
                    'matkul_id' => $matkulId,
                    'jumlah_jam' => $request->jumlah_jam[$index],
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data Mahasiswa Kompensasi berhasil disimpan',
            ]);
        }

        return redirect('/');
    }

}
