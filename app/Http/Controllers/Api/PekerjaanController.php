<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovePekerjaanModel;
use App\Models\detail_pekerjaanModel;
use App\Models\notifikasiModel;
use App\Models\PekerjaanModel;
use App\Models\PendingPekerjaanModel;
use App\Models\PengumpulanModel;
use App\Models\ProgresModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PekerjaanController extends Controller
{
    public function index()
    {
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan.persyaratan', 'progres', 'user.detailDosen', 'detail_pekerjaan.kompetensiDosen.kompetensiAdmin')->where('status', 'open')->orderby('created_at','desc')->get();

        return response()->json($pekerjaan);
    }

    public function apply(Request $request)
    {
        // Validasi inputan
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

        // Cek apakah user sudah melamar pekerjaan ini sebelumnya di pending pekerjaan
        $existingApply = PendingPekerjaanModel::where('pekerjaan_id', $request->pekerjaan_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($existingApply) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah melamar pekerjaan ini sebelumnya.',
            ], 409);
        }

        // Cek apakah user sudah melamar pekerjaan ini sebelumnya di approve pekerjaan
        $existingApplyapprove = ApprovePekerjaanModel::where('pekerjaan_id', $request->pekerjaan_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($existingApplyapprove) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah melamar pekerjaan ini sebelumnya dan sudah diterima.',
            ], 409);
        }

        // Jika belum ada, simpan data apply ke PendingPekerjaanModel
        PendingPekerjaanModel::create([
            'pekerjaan_id' => $request->pekerjaan_id,
            'user_id' => $request->user_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Lamaran pekerjaan berhasil diajukan.',
        ], 200);
    }

    public function getPelamaran($userId)
    {
        try {
            // Verifikasi apakah user yang login memiliki akses terhadap data ini
            $loggedInUserId = Auth::id();

            if ($loggedInUserId != $userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $pelamaran = PendingPekerjaanModel::with('user.detailMahasiswa.prodi', 'pekerjaan.user', 'user.kompetensi.kompetensiAdmin')
                ->whereHas('pekerjaan', function ($query) use ($loggedInUserId) {
                    $query->where('user_id', $loggedInUserId);
                })
                ->get();

            return response()->json([
                'status' => true,
                'data' => $pelamaran,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data pelamaran.',
                'error' => $e->getMessage(),
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

    public function approvePekerjaan(Request $request)
    {
        // Validasi data yang diterima dari request
        $validator = Validator::make($request->all(), [
            'pekerjaan_id' => 'required|exists:pekerjaan,pekerjaan_id',
            'user_id' => 'required|exists:m_user,user_id',
        ]);

        // Jika validasi gagal, kirimkan respons dengan status 422 dan pesan error
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Periksa apakah pelamar sudah ada pada pekerjaan ini
        $existingApprove = ApprovePekerjaanModel::where('pekerjaan_id', $request->pekerjaan_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($existingApprove) {
            return response()->json([
                'status' => false,
                'message' => 'Pelamar sudah ada pada anggota pekerjaan ini'
            ], 400); // Menggunakan status 400 untuk permintaan yang salah
        }

        // Proses persetujuan pekerjaan
        try {
            ApprovePekerjaanModel::create([
                'pekerjaan_id' => $request->pekerjaan_id,
                'user_id' => $request->user_id,
            ]);

            // Hapus pekerjaan yang masih dalam status pending
            PendingPekerjaanModel::where('user_id', $request->user_id)
                ->where('pekerjaan_id', $request->pekerjaan_id)
                ->delete();

            // Kirimkan notifikasi kepada pelamar
            notifikasiModel::create([
                'user_id' => $request->user_id,
                'pekerjaan_id' => $request->pekerjaan_id,
                'pesan' => 'Selamat!!, Anda telah diterima pada pekerjaan',
                'status' => 'belum',
                'user_id_kap' => null,
            ]);

            // Kirimkan respons sukses jika semuanya berjalan dengan baik
            return response()->json([
                'status' => true,
                'message' => 'Pekerjaan berhasil disetujui'
            ], 200); // Status 200 untuk sukses
        } catch (\Exception $e) {
            // Jika ada kesalahan, kirimkan respons error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyetujui pekerjaan',
                'error' => $e->getMessage()
            ], 500); // Status 500 untuk kesalahan server
        }
    }

    public function declinePekerjaan(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'pekerjaan_id' => 'required|exists:pekerjaan,pekerjaan_id',
            'user_id' => 'required|exists:m_user,user_id',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Hapus pekerjaan pending untuk user_id dan pekerjaan_id tertentu
            PendingPekerjaanModel::where('user_id', $request->user_id)
                ->where('pekerjaan_id', $request->pekerjaan_id)
                ->delete();

            // Buat notifikasi penolakan pekerjaan
            notifikasiModel::create([
                'user_id' => $request->user_id,
                'pekerjaan_id' => $request->pekerjaan_id,
                'pesan' => 'Mohon maaf, anda tidak diterima pada pekerjaan',
                'status' => 'belum',
                'user_id_kap' => null
            ]);

            // Kembalikan respons berhasil
            return response()->json([
                'status' => true,
                'message' => 'Pelamar berhasil ditolak'
            ], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan dan kembalikan respons error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPekerjaanPengerjaan($userId)
    {
        try {

            $login = Auth::id();

            if ($login != $userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
            // Ambil data pekerjaan berdasarkan user yang sedang login
            $tugas = PekerjaanModel::whereHas('t_approve_pekerjaan', function ($query) use ($login) {
                $query->where('user_id', $login);
            })->with(['detail_pekerjaan', 'user.detailDosen', 'progres', 'progres.pengumpulan' => function ($query) use ($login) {
                $query->where('user_id', $login);
            }])->get();

            // Buat struktur data respons
            $response = [
                'status' => true,
                'message' => 'Data berhasil diambil',
                'data' => $tugas
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Tangani jika terjadi kesalahan
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function list($login)
    {
        try {
            // Ambil ID user yang sedang login
            $userId = Auth::id();

            if ($login != $userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Ambil data pengumpulan yang sesuai dengan user yang sedang login dan berdasarkan progres_id dan pekerjaan_id
            $pengumpulan = PengumpulanModel::with('user', 'progres', 'progres.pekerjaan')
                ->where('status', 'pending')
                ->whereHas('progres.pekerjaan', function ($query) use ($login) {
                    // Kondisi untuk pekerjaan yang terkait dengan user yang sedang login
                    $query->where('user_id', $login);
                })
                ->get();

            // Kembalikan respon JSON langsung
            return response()->json([
                'success' => true,
                'message' => 'Data fetched successfully.',
                'data' => $pengumpulan,
            ], 200);
        } catch (\Exception $e) {
            // Tangani error dan kembalikan respon JSON
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function approve($id)
    {
        DB::beginTransaction(); // Start transaction

        try {
            // Retrieve the related 'pengumpulan' data with its 'user' and 'progres'
            $pengumpulan = PengumpulanModel::with('user', 'progres')->find($id);

            // Check if 'pengumpulan' exists
            if (!$pengumpulan) {
                DB::rollBack(); // Rollback if 'pengumpulan' not found
                return response()->json(['error' => 'Pengumpulan not found.'], 404);
            }

            // Check if related 'user' and 'progres' exist
            if (!$pengumpulan->progres || !$pengumpulan->user) {
                DB::rollBack(); // Rollback if no related 'progres' or 'user'
                return response()->json(['error' => 'Data progres or user not found.'], 404);
            }

            // Update the 'status' to 'accept'
            $pengumpulan->status = 'accept';
            $pengumpulan->save();

            // Access the related 'user' data
            $user = $pengumpulan->user;
            $userId = $user->user_id;
            $pekerjaanId = $pengumpulan->progres->pekerjaan_id;

            // Access the 'jamKompen' related to the user
            $jamKompen = $user->jamKompen;

            // Check if 'jamKompen' exists
            if ($jamKompen) {
                $currentAkumulasiJam = $jamKompen->akumulasi_jam;
                $jamKompenProgres = $pengumpulan->progres->jam_kompen; // 'jam_kompen' from 'progres'

                // Check if there are enough hours to deduct
                if ($currentAkumulasiJam >= $jamKompenProgres) {
                    // Deduct the 'jam_kompen' value
                    $jamKompen->akumulasi_jam -= $jamKompenProgres;
                    $jamKompen->save(); // Save the changes
                } else {
                    // If not enough, set 'akumulasi_jam' to 0
                    $jamKompen->akumulasi_jam = 0;
                    $jamKompen->save();
                }

                // Create a notification for the user
                notifikasiModel::create([
                    'user_id' => $userId,
                    'pekerjaan_id' => $pekerjaanId,
                    'pesan' => 'Jam Kompen Anda Berkurang!!, pengumpulan anda telah dinilai',
                    'status' => 'belum',
                    'user_id_kap' => null
                ]);

                DB::commit(); // Commit transaction if successful

                // Return success response
                return response()->json([
                    'message' => 'Tugas berhasil disetujui.',
                    'data' => $pengumpulan
                ], 200);
            } else {
                DB::rollBack(); // Rollback if 'jamKompen' is not found
                return response()->json(['error' => 'Jam Kompen not found for this user.'], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback if any error occurs

            // Return error response
            return response()->json([
                'error' => 'An error occurred while approving the task: ' . $e->getMessage()
            ], 500);
        }
    }

    public function decline($id)
    {
        // Mencari pengumpulan berdasarkan ID dan relasi user serta progres
        $pengumpulan = PengumpulanModel::with('user', 'progres')->find($id);

        // Jika data pengumpulan tidak ditemukan, kembalikan error 404
        if (!$pengumpulan) {
            return response()->json([
                'message' => 'Pengumpulan tidak ditemukan.',
            ], 404);
        }

        // Mengubah status pengumpulan menjadi 'decline'
        $pengumpulan->status = 'decline';
        $pengumpulan->save();

        // Mendapatkan user_id dan pekerjaan_id dari relasi
        $userId = $pengumpulan->user->user_id;
        $pekerjaanId = $pengumpulan->progres->pekerjaan_id;

        // Membuat notifikasi untuk user
        notifikasiModel::create([
            'user_id' => $userId,
            'pekerjaan_id' => $pekerjaanId,
            'pesan' => 'Mohon Maaf Pengumpulan Anda Ditolak, coba kumpulkan pekerjaan dengan baik.',
            'status' => 'belum', // status belum dibaca
            'user_id_kap' => null, // opsional jika ada kolom user_id_kap
        ]);

        // Mengembalikan response dengan status success
        return response()->json([
            'message' => 'Pengumpulan tugas berhasil ditolak.',
            'data' => $pengumpulan
        ], 200); // Menggunakan kode status HTTP 200 OK
    }

    public function getSelesai($login)
{
    try {

        $user = Auth::id();
        if ($login!=$user) {
            return response()->json([
                'success' => false,
                'message' => 'unauthorize',
            ]);
        }
        // Mengambil nama pengguna yang memiliki semua pengumpulan dengan status 'accept' untuk setiap progres
        $data = DB::table('m_user as u')
            ->join('pengumpulan as pg', 'u.user_id', '=', 'pg.user_id')
            ->join('progres as pr', 'pg.progres_id', '=', 'pr.progres_id')
            ->join('pekerjaan as p', 'pr.pekerjaan_id', '=', 'p.pekerjaan_id')
            ->groupBy('u.user_id', 'p.pekerjaan_id')
            ->havingRaw('COUNT(CASE WHEN pg.status != "accept" THEN 1 END) = 0')
            ->havingRaw('COUNT(pr.progres_id) = (SELECT COUNT(*) FROM progres pr2 WHERE pr2.pekerjaan_id = p.pekerjaan_id)')
            ->select('u.nama', 'u.username', 'p.pekerjaan_nama')
            ->distinct() // Untuk menghindari duplikasi nama pengguna
            ->where('p.user_id', $login) // Filter berdasarkan user yang sedang login
            ->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada data pekerjaan yang selesai.',
                    'data' => [],
                ], 200);
            }else {
                return response()->json([
                    'success' => true,
                    'data' => $data,
                ], 200);
            }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
        ], 500);
    }
}

}
