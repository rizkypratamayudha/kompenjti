<?php

namespace App\Http\Controllers;

use App\Models\detail_pekerjaanModel;
use App\Models\PekerjaanModel;
use App\Models\ProgresModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PekerjanController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title' => 'Buat Pekerjaan',
            'list'=> ['Home','Buat Pekerjaan']
        ];

        $page = (object)[
            'title' => 'Page Buat Pekerjaan'
        ];

        $activeMenu = 'pekerjaan';
        $pekerjaan = PekerjaanModel::all();
        return  view('dosen.index',['breadcrumb' => $breadcrumb,'page'=> $page,'activeMenu'=> $activeMenu,'tugas' => $pekerjaan]);
    }

    public function create_ajax(){
        return view('dosen.create_ajax');
    }

    public function store_ajax(Request $request){
        $validator = Validator::make($request->all(),[
            'jenis_pekerjaan' => 'required|string|in:Teknis,Pengabdian,Penelitian',
            'pekerjaan_nama' => 'required|string|max:255',
            'jumlah_jam_kompen' => 'required|integer|min:0',
            'jumlah_anggota' => 'required|integer|min:1',
            'persyaratan' => 'array|nullable',
            'persyaratan.*' => 'string|max:50',
            'deskripsi_tugas' => 'nullable|string|max:1000',
            'judul_progres' => 'required|array|min:1',
            'judul_progres.*' => 'required|string|max:255',
            'jam_kompen' => 'required|array|min:1',
            'jam_kompen.*' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user_id = Auth::user()->id;

        DB::beginTransaction();

        $pekerjaan = PekerjaanModel::create([
            'user_id' => $user_id,
            'jenis_pekerjaan' => $request->jenis_pekerjaan,
            'pekerjaan_nama' => $request->pekerjaan_nama,
            'jumlah_jam_kompen' => $request->jumlah_jam_kompen
        ]);

        $detailPekerjaan = detail_pekerjaanModel::create([
            'pekerjaan_id' => $pekerjaan->id,
            'jumlah_anggota' => $request->jumlah_anggota,
            'persyaratan' => json_encode($request->persyaratan), // Simpan sebagai JSON jika array
            'deskripsi_tugas' => $request->deskripsi_tugas
        ]);

        foreach ($request->judul_progres as $index => $judul) {
            ProgresModel::create([
                'pekerjaan_id' => $pekerjaan->id,
                'judul_progres' => $judul,
                'jam_kompen' => $request->jam_kompen[$index]
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data pekerjaan berhasil disimpan'
        ]);
    }
}
