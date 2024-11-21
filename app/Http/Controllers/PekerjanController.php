<?php

namespace App\Http\Controllers;

use App\Models\ApprovePekerjaanModel;
use App\Models\detail_dosenModel;
use App\Models\detail_pekerjaanModel;
use App\Models\kompetensi_adminModel;
use App\Models\kompetensi_dosenModel;
use App\Models\kompetensiModel;
use App\Models\PekerjaanModel;
use App\Models\PendingPekerjaanController;
use App\Models\PendingPekerjaanModel;
use App\Models\PeriodeModel;
use App\Models\PersyaratanModel;
use App\Models\ProgresModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PekerjanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Buat Pekerjaan',
            'list' => ['Home', 'Buat Pekerjaan']
        ];

        $page = (object)[
            'title' => 'Page Buat Pekerjaan'
        ];

        $activeMenu = 'dosen';
        $activeTab = 'progres'; // Menetapkan tab aktif default ke 'progres'

        $pekerjaan = PekerjaanModel::with('detail_pekerjaan', 'progres')->where('user_id', Auth::id())->get();

        return view('dosen.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'tugas' => $pekerjaan,
            'activeTab' => $activeTab
        ]);
    }


    public function create_ajax()
    {
        $kompetensi = kompetensi_adminModel::all();
        return view('dosen.create_ajax', ['kompetensi' => $kompetensi]);
    }

    public function store_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_pekerjaan' => 'required|string|in:Teknis,Pengabdian,Penelitian',
            'pekerjaan_nama' => 'required|string|max:255',
            'jumlah_anggota' => 'required|integer|min:1',
            'persyaratan' => ['nullable', 'string', 'json'],
            'persyaratan.*' => 'string|max:50',
            'kompetensi_id' => 'nullable|array',
            'kompetensi_id.*' => 'required|integer',
            'deskripsi_tugas' => 'nullable|string|max:1000',
            'judul_progres' => 'required|array|min:1',
            'judul_progres.*' => 'required|string|max:255',
            'hari' => 'required|array|min:1',
            'hari.*' => 'required|string|max:20',
            'jam_kompen' => 'required|array|min:1',
            'jam_kompen.*' => 'required|integer|min:1',
            'status' => 'required|in:open,close,done'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user_id = Auth::id();
        $jumlah_jam_kompen = array_sum($request->jam_kompen);

        DB::beginTransaction();
        try {
            $pekerjaan = PekerjaanModel::create([
                'user_id' => $user_id,
                'jenis_pekerjaan' => $request->jenis_pekerjaan,
                'pekerjaan_nama' => $request->pekerjaan_nama,
                'jumlah_jam_kompen' => $jumlah_jam_kompen,
                'status' => $request->status,
            ]);

            $detailPekerjaan = detail_pekerjaanModel::create([
                'pekerjaan_id' => $pekerjaan->pekerjaan_id,
                'jumlah_anggota' => $request->jumlah_anggota,
                'deskripsi_tugas' => $request->deskripsi_tugas
            ]);

            if (!empty($request->persyaratan)) {
                $persyaratanArray = json_decode($request->persyaratan, true); // Decode JSON ke array
                foreach ($persyaratanArray as $persyaratanNama) {
                    DB::table('persyaratan')->insert([
                        'detail_pekerjaan_id' => $detailPekerjaan->detail_pekerjaan_id,
                        'persyaratan_nama' => $persyaratanNama
                    ]);
                }
            }
            if (!empty($request->kompetensi_id)) {
                foreach ($request->kompetensi_id as $kompetensiId) {
                    DB::table('kompetensi_dosen')->insert([
                        'detail_pekerjaan_id' => $detailPekerjaan->detail_pekerjaan_id,
                        'kompetensi_admin_id' => $kompetensiId
                    ]);
                }
            }



            foreach ($request->judul_progres as $index => $judul) {
                ProgresModel::create([
                    'pekerjaan_id' => $pekerjaan->pekerjaan_id,
                    'judul_progres' => $judul,
                    'hari' => $request->hari[$index],
                    'jam_kompen' => $request->jam_kompen[$index]
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data pekerjaan berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function enter_pekerjaan(string $id)
    {
        $breadcrumb = (object)[
            'title' => 'Pekerjaan',
            'list' => ['Home', 'Pekerjaan']
        ];

        $page = (object)[
            'title' => 'Pekerjaan'
        ];

        $activeMenu = 'dosen';
        $activeTab = 'progres';
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan', 'progres')->where('pekerjaan_id', $id)->first();
        return view('dosen.pekerjaan', ['pekerjaan' => $pekerjaan, 'breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'activeTab' => $activeTab]);
    }

    public function getProgres(Request $request, $id)
    {
        $progres = ProgresModel::where('pekerjaan_id', $id)->get();
        if ($progres->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada data progres untuk pekerjaan ini.'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $progres
        ]);
    }

    public function getPelamaran($id)
    {

        $pelamaran = PendingPekerjaanModel::with('user.detailMahasiswa.prodi')->where('pekerjaan_id', $id)->get();

        return response()->json([
            'status' => true,
            'data' => $pelamaran,
        ]);
    }

    public function getAnggota($id)
    {
        $anggota = ApprovePekerjaanModel::with('user.detailMahasiswa.prodi')->where('pekerjaan_id', $id)->get();

        return response()->json([
            'status' => true,
            'data' => $anggota
        ]);
    }

    public function show_ajax($id)
    {
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan.persyaratan','detail_pekerjaan.kompetensiDosen.kompetensiAdmin')->where('pekerjaan_id', $id)->first();
        $jumlahProgres = ProgresModel::where('pekerjaan_id', $id)->count();

        return view('pekerjaanMHS.show_ajax', [
            'pekerjaan' => $pekerjaan,
            'jumlahProgres' => $jumlahProgres,
            'persyaratan' => $pekerjaan->detail_pekerjaan->persyaratan ?? collect(),
            'kompetensi' => $pekerjaan->detail_pekerjaan->kompetensiDosen ?? collect(),
        ]);
    }

    public function approvePekerjaan(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        $existtingApprove = ApprovePekerjaanModel::where('pekerjaan_id', $request->pekerjaan_id)->where('user_id', $request->user_id)->exists();
        if ($existtingApprove) {
            return response()->json([
                'status' => false,
                'message' => 'Pelamar sudah ada pada anggota'
            ]);
        }
        ApprovePekerjaanModel::create([
            'pekerjaan_id' => $request->pekerjaan_id,
            'user_id' => $request->user_id,
        ]);

        PendingPekerjaanModel::where('user_id', $request->user_id)->where('pekerjaan_id', $request->pekerjaan_id)->delete();

        return response()->json(['status' => true, 'message' => 'Pekerjaan berhasil disetujui']);
    }

    public function declinePekerjaan(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        PendingPekerjaanModel::where('user_id', $request->user_id)->where('pekerjaan_id', $request->pekerjaan_id)->delete();
        return response()->json(['status' => true, 'message' => 'Pelamar berhasil ditolak']);
    }

    public function kickPekerjaan(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        ApprovePekerjaanModel::where('user_id', $request->user_id)->where('pekerjaan_id', $request->pekerjaan_id)->delete();
        return response()->json(['status' => true, 'message' => 'Anggota berhasil dikick']);
    }

    public function edit_ajax($id)
    {
        $kompetensi = kompetensi_adminModel::all();
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan', 'progres', 'detail_pekerjaan.persyaratan','detail_pekerjaan.kompetensiDosen')->find($id);
        return view('dosen.setting', ['pekerjaan' => $pekerjaan,'kompetensi'=>$kompetensi]);
    }

    public function update_ajax(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jenis_pekerjaan' => 'required|string|in:Teknis,Pengabdian,Penelitian',
            'pekerjaan_nama' => 'required|string|max:255',
            'jumlah_anggota' => 'required|integer|min:1',
            'persyaratan' => ['nullable', 'string', 'json'],
            'persyaratan.*' => 'string|max:50',
            'kompetensi_id' => 'nullable|array',
            'kompetensi_id.*' => 'required|integer',
            'deskripsi_tugas' => 'nullable|string|max:1000',
            'judul_progres' => 'required|array|min:1',
            'judul_progres.*' => 'required|string|max:255',
            'hari' => 'required|array|min:1',
            'hari.*' => 'required|string|max:20',
            'jam_kompen' => 'required|array|min:1',
            'jam_kompen.*' => 'required|integer|min:1',
            'status' => 'sometimes|in:open,close,done'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $pekerjaan = PekerjaanModel::find($id);
        $user_id = Auth::id();
        $jumlah_jam_kompen = array_sum($request->jam_kompen);

        DB::beginTransaction();
        try {
            $pekerjaan->update([
                'jenis_pekerjaan' => $request->jenis_pekerjaan,
                'pekerjaan_nama' => $request->pekerjaan_nama,
                'jumlah_jam_kompen' => $jumlah_jam_kompen,
                'status' => $request->status,
            ]);

            if ($pekerjaan->detail_pekerjaan) {
                $pekerjaan->detail_pekerjaan->update([
                    'jumlah_anggota' => $request->jumlah_anggota,
                    'deskripsi_tugas' => $request->deskripsi_tugas,
                ]);
            }

            if (!empty($request->persyaratan)) {
                DB::table('persyaratan')
                    ->where('detail_pekerjaan_id', $pekerjaan->detail_pekerjaan->detail_pekerjaan_id)
                    ->delete();

                $persyaratanArray = json_decode($request->persyaratan, true);
                foreach ($persyaratanArray as $persyaratanNama) {
                    DB::table('persyaratan')->insert([
                        'detail_pekerjaan_id' => $pekerjaan->detail_pekerjaan->detail_pekerjaan_id,
                        'persyaratan_nama' => $persyaratanNama
                    ]);
                }
            }

            if (!empty($request->kompetensi_id)) {
                foreach ($request->kompetensi_id as $kompetensiId) {
                    DB::table('kompetensi_dosen')->updateOrInsert([
                        'detail_pekerjaan_id' => $pekerjaan->detail_pekerjaan->detail_pekerjaan_id,
                        'kompetensi_admin_id' => $kompetensiId
                    ]);
                }
            }

            foreach ($request->judul_progres as $index => $judul) {
                ProgresModel::updateOrCreate(
                    ['pekerjaan_id' => $pekerjaan->pekerjaan_id, 'judul_progres' => $judul],
                    [
                        'hari' => $request->hari[$index],
                        'jam_kompen' => $request->jam_kompen[$index]
                    ]
                );
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data pekerjaan berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function get_anggota($id)
    {
        $anggotaJumlah = ApprovePekerjaanModel::where('pekerjaan_id', $id)->count();
        return response()->json([
            'status' => true,
            'anggotaJumlah' => $anggotaJumlah
        ]);
    }

    public function lihatPekerjaan($id)
    {
        $user = UserModel::find($id);
        $kompetensi = kompetensiModel::with('kompetensiAdmin')->where('user_id',$id)->get();

        return view('dosen.lihat_pekerjaan', ['user' => $user, 'kompetensi' => $kompetensi]);
    }
}
