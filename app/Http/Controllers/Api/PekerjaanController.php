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


    public function store(Request $request)
    {
        // Validasi input yang diterima melalui API
        $validator = Validator::make($request->all(), [
            'jenis_pekerjaan' => 'required|string|in:Teknis,Pengabdian,Penelitian',
            'pekerjaan_nama' => 'required|string|max:255',
            'jumlah_anggota' => 'required|integer|min:1',
            'persyaratan' => 'array|nullable',
            'persyaratan.*' => 'string|max:50',
            'deskripsi_tugas' => 'nullable|string|max:1000',
            'judul_progres' => 'required|array|min:1',
            'judul_progres.*' => 'required|string|max:255',
            'hari' => 'required|array|min:1',
            'hari.*' => 'required|string|max:20',
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

        // Dapatkan ID pengguna yang sedang login
        $user_id = Auth::id();
        $jumlah_jam_kompen = array_sum($request->jam_kompen);

        DB::beginTransaction();
        try {
            // Simpan data utama pekerjaan
            $pekerjaan = PekerjaanModel::create([
                'user_id' => $user_id,
                'jenis_pekerjaan' => $request->jenis_pekerjaan,
                'pekerjaan_nama' => $request->pekerjaan_nama,
                'jumlah_jam_kompen' => $jumlah_jam_kompen,
            ]);

            // Simpan detail pekerjaan
            $detailPekerjaan = detail_pekerjaanModel::create([
                'pekerjaan_id' => $pekerjaan->pekerjaan_id,
                'jumlah_anggota' => $request->jumlah_anggota,
                'persyaratan' => json_encode($request->persyaratan),
                'deskripsi_tugas' => $request->deskripsi_tugas
            ]);

            // Simpan setiap progres
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
}
