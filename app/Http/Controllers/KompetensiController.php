<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\kompetensiModel;
use App\Models\detail_mahasiswaModel;
use App\Models\kompetensi_adminModel;
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
        $kompetensi = kompetensiModel::with('user.periode', 'kompetensiAdmin')->where('user_id', Auth::id())->get();
        return view('kompetensi.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kompetensi' => $kompetensi, 'periodeNama' => $periodeNama, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $kompetensi = kompetensiModel::with(['kompetensiAdmin', 'user'])
            ->where('user_id', Auth::id());

        if ($request->periode_id) {
            $kompetensi->whereHas('user.periode', function ($query) use ($request) {
                $query->where('periode_id', $request->periode_id);
            });
        }

        return DataTables::of($kompetensi)
            ->addIndexColumn()
            ->addColumn('kompetensi_nama', function ($kompetensi) {
                // Ambil nama dari relasi kompetensiAdmin
                return $kompetensi->kompetensiAdmin->kompetensi_nama ?? '-';
            })
            ->addColumn('aksi', function ($kompetensi) {
                $btn  = '<button onclick="modalAction(\'' . url('/kompetensi/' . $kompetensi->kompetensi_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kompetensi/' . $kompetensi->kompetensi_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kompetensi/' . $kompetensi->kompetensi_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }



    public function create_ajax()
    {
        $user = UserModel::with('detailMahasiswa', 'detailMahasiswa.prodi')->where('user_id', Auth::id())->first();
        $kompetensi = kompetensi_adminModel::all();
        return view('kompetensi.create_ajax', ['user' => $user, 'kompetensi' => $kompetensi]);
    }

    public function store(Request $request)
    {
        // Validasi data input
        $validator = Validator::make($request->all(), [

            'kompetensi_id' => 'required|integer',
            'pengalaman' => 'required|string',
            'bukti' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Proses simpan data
        try {
            // Simpan ke database
            KompetensiModel::create([
                'user_id' => Auth::id(),
                'kompetensi_admin_id' => $request->kompetensi_id,
                'pengalaman' => $request->pengalaman,
                'bukti' => $request->bukti
            ]);

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

    public function show_ajax($id)
    {
        $user = UserModel::find(Auth::id());
        $kompetensi = kompetensiModel::with('kompetensiAdmin')->where('kompetensi_id', $id)->get();

        return view('kompetensi.show_ajax', ['user' => $user, 'kompetensi' => $kompetensi]);
    }

    public function edit_ajax($id)
    {
        $user = UserModel::find(Auth::id());
        $kompetensi = kompetensiModel::with('kompetensiAdmin')->where('kompetensi_id', $id)->first();
        $kompetensi_admin = kompetensi_adminModel::all();
        return view('kompetensi.edit_ajax', ['user' => $user, 'kompetensi' => $kompetensi, 'kompetensi_admin' => $kompetensi_admin]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kompetensi_id' => 'required|integer',
                'pengalaman' => 'required|string',
                'bukti' => 'required|string'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,    // respon json, true: berhasil, false: gagal
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()  // menunjukkan field mana yang error
                ]);
            }

            $check = kompetensiModel::find($id);
            if ($check){
                $check->update($request->all());
                return response()->json([
                    'status'=> true,
                    'message'=> 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status'=> false,
                    'message'=> 'Data tidak ditemukan'
                ]);
            }
        }
    }
}
