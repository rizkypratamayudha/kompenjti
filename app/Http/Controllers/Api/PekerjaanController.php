<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovePekerjaanModel;
use App\Models\detail_pekerjaanModel;
use App\Models\notifikasiModel;
use App\Models\PekerjaanModel;
use App\Models\PendingPekerjaanModel;
use App\Models\ProgresModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PekerjaanController extends Controller
{
    public function index()
    {
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan.persyaratan', 'progres', 'user.detailDosen', 'detail_pekerjaan.kompetensiDosen.kompetensiAdmin')->where('status', 'open')->get();

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
            })->with(['progres', 'progres.pengumpulan' => function ($query) use ($login) {
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

    
}
