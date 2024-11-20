<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\kompetensiModel;
use App\Models\detail_mahasiswaModel;
use App\Models\PeriodeModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class KompetensiController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Kompetensi Mahasiswa',
            'list' => ['Home', 'Kompetensi Mahasiswa'],
        ];

        $page = (object)[
            'title' => 'Page Kompetensi Mahasiswa'
        ];

        $activeMenu = 'kompetensi';

        $periodeNama = PeriodeModel::all();
        $kompetensi = kompetensiModel::with('user.periode')->where('user_id', Auth::id())->get();
        return view('kompetensi.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kompetensi' => $kompetensi, 'periodeNama' => $periodeNama, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $kompetensi = kompetensiModel::where('user_id', Auth::id())
            ->select('kompetensi_id', 'user_id', 'kompetensi_nama', 'pengalaman', 'bukti')
            ->with('user.periode');

            if ($request->periode_id) {
                $kompetensi->whereHas('user.detailMahasiswa.periode', function ($query) use ($request) {
                    $query->where('periode_id', $request->periode_id);
                });
            }


        return DataTables::of($kompetensi)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kompetensi) {
                $btn  = '<button onclick="modalAction(\'' . url('/kompetensi/' . $kompetensi->kompetensi_id .
                    '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kompetensi/' . $kompetensi->kompetensi_id .
                    '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kompetensi/' . $kompetensi->kompetensi_id .
                    '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax(){
        $user = UserModel::with('detailMahasiswa')->where('user_id',Auth::id())->first();
        return view('kompetensi.create_ajax',['user'=>$user]);
    }

    public function store(Request $request)
    {
        // Validasi data input
        $validator = Validator::make($request->all(), [

            'kompetensi.*.nama' => 'required|string|max:255',
            'kompetensi.*.pengalaman' => 'required|string|max:255',
            'kompetensi.*.bukti' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Proses simpan data
        try {
            foreach ($request->kompetensi as $data) {

                // Simpan ke database
                KompetensiModel::create([
                    'user_id' => Auth::id(),
                    'kompetensi_nama' => $data['nama'],
                    'pengalaman' => $data['pengalaman'],
                    'bukti' => $data['bukti'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data kompetensi berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show_ajax($id){
        $user = UserModel::find( Auth::id() );
        $kompetensi = kompetensiModel::where('kompetensi_id',$id)->get();

        return view('kompetensi.show_ajax',['user'=>$user,'kompetensi'=>$kompetensi]);
    }
}
