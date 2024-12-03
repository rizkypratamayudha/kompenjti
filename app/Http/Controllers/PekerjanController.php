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
use App\Models\PengumpulanModel;
use App\Models\PeriodeModel;
use App\Models\PersyaratanModel;
use App\Models\ProgresModel;
use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

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
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan.persyaratan', 'detail_pekerjaan.kompetensiDosen.kompetensiAdmin')->where('pekerjaan_id', $id)->first();
        $jumlahProgres = ProgresModel::where('pekerjaan_id', $id)->count();

        return view('pekerjaanMHS.show_ajax', [
            'pekerjaan' => $pekerjaan,
            'jumlahProgres' => $jumlahProgres,
            'persyaratan' => $pekerjaan->detail_pekerjaan->persyaratan ?? collect(),
            'kompetensi' => $pekerjaan->detail_pekerjaan->kompetensiDosen ?? collect(),
        ]);
    }

    public function delete_ajax(Request $request, string $pekerjaan_id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $pekerjaan = PekerjaanModel::find($pekerjaan_id);

            if (!$pekerjaan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Pekerjaan tidak ditemukan'
                ]);
            }

            try {
                // Hapus semua data terkait secara bertahap
                $pekerjaan->detail_pekerjaan->each(function ($detail) {
                    // Hapus persyaratan dan kompetensi dosen terkait
                    $detail->persyaratan()->delete();
                    $detail->kompetensiDosen()->delete();
                });

                // Hapus data detail pekerjaan
                $pekerjaan->detail_pekerjaan()->delete();

                $pekerjaan->progres()->delete();
                // Hapus data pekerjaan itu sendiri
                $pekerjaan->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data Pekerjaan berhasil dihapus'
                ]);
            } catch (\Exception $e) {
                // Tangani error lainnya
                return response()->json([
                    'status' => false,
                    'message' => 'Data gagal dihapus: ' . $e->getMessage()
                ]);
            }
        }

        return redirect('/');
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
        $pekerjaan = PekerjaanModel::with([
            'detail_pekerjaan',
            'progres',
            'detail_pekerjaan.persyaratan',
            'detail_pekerjaan.kompetensiDosen'
        ])->find($id);

        // Get kompetensi IDs directly from the kompetensiDosen relationship
        $selectedKompetensiIds = $pekerjaan->detail_pekerjaan->kompetensiDosen
            ->pluck('kompetensi_admin_id')
            ->toArray();

        return view('dosen.setting', [
            'pekerjaan' => $pekerjaan,
            'kompetensi' => $kompetensi,
            'selectedKompetensiIds' => $selectedKompetensiIds
        ]);
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
            'hari.*' => 'required|integer|min:1',
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
        if (!$pekerjaan) {
            return response()->json([
                'status' => false,
                'message' => 'Pekerjaan tidak ditemukan'
            ], 404);
        }

        $jumlah_jam_kompen = array_sum($request->jam_kompen);

        DB::beginTransaction();
        try {
            // Update data pekerjaan
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

            // Update persyaratan jika ada
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

            // Update kompetensi jika ada
            if (!empty($request->kompetensi_id)) {
                foreach ($request->kompetensi_id as $kompetensiId) {
                    DB::table('kompetensi_dosen')->updateOrInsert([
                        'detail_pekerjaan_id' => $pekerjaan->detail_pekerjaan->detail_pekerjaan_id,
                        'kompetensi_admin_id' => $kompetensiId
                    ]);
                }
            }

            // Update progres
            $existingProgres = $pekerjaan->progres()->orderBy('deadline', 'asc')->get();

            foreach ($request->judul_progres as $index => $judul) {
                $hari = $request->hari[$index];
                $jamKompen = $request->jam_kompen[$index];

                if (isset($existingProgres[$index])) {
                    $previousDeadline = $index > 0
                        ? Carbon::parse($existingProgres[$index - 1]->deadline)
                        : Carbon::now();

                    $newDeadline = $previousDeadline->copy()->addDays($hari);

                    // Update progres yang relevan
                    $existingProgres[$index]->update([
                        'judul_progres' => $judul,
                        'hari' => $hari,
                        'jam_kompen' => $jamKompen,
                        'deadline' => $newDeadline,
                    ]);
                } else {
                    $lastDeadline = $index > 0
                        ? Carbon::parse($existingProgres[$index - 1]->deadline)
                        : Carbon::now();

                    $newDeadline = $lastDeadline->copy()->addDays($hari);

                    // Tambahkan progres baru
                    ProgresModel::create([
                        'pekerjaan_id' => $pekerjaan->pekerjaan_id,
                        'judul_progres' => $judul,
                        'hari' => $hari,
                        'jam_kompen' => $jamKompen,
                        'deadline' => $newDeadline,
                    ]);
                }
            }

            // Update akumulasi_deadline
            $latestProgres = $pekerjaan->progres()->orderBy('deadline', 'desc')->first();
            if ($latestProgres) {
                $pekerjaan->update([
                    'akumulasi_deadline' => $latestProgres->deadline,
                ]);
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
        $kompetensi = kompetensiModel::with('kompetensiAdmin')->where('user_id', $id)->get();

        return view('dosen.lihat_pekerjaan', ['user' => $user, 'kompetensi' => $kompetensi]);
    }

    public function hitung_notif_pelamar($id)
    {
        $jumlah = PendingPekerjaanModel::where('pekerjaan_id', $id)->count();
        return response()->json(['jumlah' => $jumlah]);
    }

    public function mulai($id)
    {
        // Ambil data pekerjaan berdasarkan ID
        $pekerjaan = PekerjaanModel::find($id);

        if (!$pekerjaan) {
            return redirect()->route('dosen.index')->with('error', 'Pekerjaan tidak ditemukan.');
        }

        // Ambil data progres berdasarkan pekerjaan_id dan urutkan
        $progresList = ProgresModel::where('pekerjaan_id', $id)->orderBy('progres_id')->get();

        if ($progresList->isEmpty()) {
            return redirect()->route('dosen.index')->with('error', 'Tidak ada progres untuk pekerjaan ini.');
        }

        // Inisialisasi deadline pertama dari waktu saat ini
        $deadlinepertama = Carbon::now();

        // Iterasi setiap progres untuk memperbarui deadline dan menghitung akumulasi deadline
        foreach ($progresList as $progres) {
            // Mengambil jumlah hari dari setiap progres
            $hari = $progres->hari;

            // Menambahkan hari ke deadline
            $deadlineBaru = $deadlinepertama->copy()->addDays($hari);

            // Update deadline untuk progres ini
            $progres->update([
                'deadline' => $deadlineBaru,
            ]);

            // Set deadline pertama untuk progres berikutnya
            $deadlinepertama = $deadlineBaru;
        }

        // Update akumulasi_deadline pada tabel pekerjaan (berisi deadline terakhir)
        $pekerjaan->update([
            'akumulasi_deadline' => $deadlinepertama,  // Menggunakan deadline terakhir
        ]);

        // Kirimkan pesan sukses melalui session
        return redirect()->route('dosen.index')->with('success', 'Pekerjaan telah dimulai, semua progres sudah diperbarui!');
    }

    public function enter_progres($id)
    {
        $breadcrumg = (object) [
            'title' => 'Progres',
            'list' => ['Home', 'Tambah Pekerjaan', 'Progres']
        ];

        $page = (object)[
            'title' => 'Page Progres',
        ];

        $activeMenu = 'dosen';
        $pengumpulan = PengumpulanModel::with('user', 'progres')->where('progres_id', $id)->first();
        $progres = ProgresModel::where('progres_id', $id)->first();
        return view('dosen.progres', ['breadcrumb' => $breadcrumg, 'page' => $page, 'activeMenu' => $activeMenu, 'pengumpulan' => $pengumpulan, 'progres' => $progres]);
    }

    public function list(Request $request, $id)
    {
        $pengumpulan = PengumpulanModel::with('user', 'progres') // Ambil relasi user dan progres
            ->where('progres_id', $id);

        return DataTables::of($pengumpulan)
            ->addIndexColumn()
            ->addColumn('username', function ($pengumpulan) {
                return $pengumpulan->user->username ?? '-';
            })
            ->addColumn('nama', function ($pengumpulan) {
                return $pengumpulan->user->nama ?? '-';
            })
            ->addColumn('status', function ($pengumpulan) {
                return $pengumpulan->status === 'pending' ? 'Sudah Diserahkan' : $pengumpulan->status = 'Sudah Dinilai';
            })
            ->addColumn('aksi', function ($pengumpulan) {
                $btn  = '<button onclick="modalAction(\'' . url('/dosen/' . $pengumpulan->pengumpulan_id .
                    '/detail-progres') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function detail_progres($id)
    {
        $pengumpulan = PengumpulanModel::with('user', 'progres')->where('pengumpulan_id', $id)->find($id);
        return view('dosen.detail_progres', ['pengumpulan' => $pengumpulan]);
    }

    public function approve($id)
{
    DB::beginTransaction(); // Mulai transaksi
    try {
        // Ambil data pengumpulan yang terkait dengan progres dan user
        $pengumpulan = PengumpulanModel::with('user', 'progres')->findOrFail($id);

        if (!$pengumpulan->progres || !$pengumpulan->user) {
            DB::rollBack(); // Rollback jika ada kesalahan
            return response()->json(['error' => 'Data progres atau user tidak ditemukan.'], 404);
        }

        // Mengubah status pengumpulan menjadi 'accept'
        $pengumpulan->status = 'accept';
        $pengumpulan->save();

        // Mengakses user yang terkait dengan pengumpulan
        $user = $pengumpulan->user;

        // Mengakses jam_kompen yang terkait dengan user
        $jamKompen = $user->jamKompen;

        if ($jamKompen) {
            // Mengambil nilai akumulasi_jam
            $currentAkumulasiJam = $jamKompen->akumulasi_jam;
            $jamKompenProgres = $pengumpulan->progres->jam_kompen; // Jam kompen yang diambil dari progres

            // Mengecek apakah cukup untuk mengurangi
            if ($currentAkumulasiJam >= $jamKompenProgres) {
                // Kurangi akumulasi_jam
                $jamKompen->akumulasi_jam -= $jamKompenProgres;
                $jamKompen->save(); // Simpan perubahan
            } else {
                // Set akumulasi_jam menjadi 0 jika tidak cukup
                $jamKompen->akumulasi_jam = 0;
                $jamKompen->save();
            }

            DB::commit(); // Commit jika berhasil
            return response()->json(['message' => 'Tugas berhasil disetujui.']);
        } else {
            DB::rollBack(); // Rollback jika jamKompen tidak ditemukan
            return response()->json(['error' => 'Jam Kompen tidak ditemukan untuk user tersebut.'], 404);
        }
    } catch (\Exception $e) {
        DB::rollBack(); // Rollback transaksi jika terjadi error
        return response()->json(['error' => 'Terjadi kesalahan saat menyetujui tugas: ' . $e->getMessage()], 500);
    }
}


}
