<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\notifikasiModel;
use App\Models\t_approve_cetak_model;
use App\Models\t_pending_cetak_model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaprodiController extends Controller
{
    public function index()
{
    try {
        // Mendapatkan prodi_id dari Kaprodi yang sedang login
        $kaprodiProdiId = Auth::user()->detailKaprodi->prodi->prodi_id;

        // Query data t_pending_cetak_model dengan filter prodi_id sesuai Kaprodi
        $penerimaan = t_pending_cetak_model::select('t_pending_cetak_id', 'user_id', 'pekerjaan_id', 'created_at', 'updated_at')
            ->with(['user', 'pekerjaan'])
            ->whereHas('user.detailMahasiswa.prodi', function ($query) use ($kaprodiProdiId) {
                $query->where('prodi_id', $kaprodiProdiId);
            })
            ->get();  // Ambil data tanpa DataTables

        // Jika data ditemukan atau tidak ditemukan, kembalikan dengan response sukses
        return response()->json([
            'success' => true,
            'data' => $penerimaan // Jika tidak ada data, data akan kosong, tapi tetap sukses
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}


    public function approve(Request $request)
{
    try {
        $user = Auth::id();

        // Mengecek apakah data tidak ditemukan


        // Mengecek apakah sudah ada data dengan kombinasi user_id dan pekerjaan_id di t_approve_cetak_model
        $existingRecord = t_approve_cetak_model::where('user_id', $request->user_id)
            ->where('pekerjaan_id', $request->pekerjaan_id)
            ->first();

        // Jika data sudah ada, return error
        if ($existingRecord) {
            return response()->json(['error' => 'Surat ini sudah disetujui sebelumnya'], 409);
        }

        // Jika data belum ada, lanjutkan proses approval
        $cetak = t_approve_cetak_model::create([
            'user_id' => $request->user_id,
            'pekerjaan_id' => $request->pekerjaan_id,
            'user_id_kap' => $user
        ]);

        // Hapus data dari t_pending_cetak_model setelah approval
        t_pending_cetak_model::where('user_id',$request->user_id)->where('pekerjaan_id',$request->pekerjaan_id)->delete();

        // Menyimpan notifikasi untuk user
        notifikasiModel::create([
            'user_id' => $request->user_id,
            'pekerjaan_id' => $request->pekerjaan_id,
            'pesan' => 'Selamat!!, Request Cetak Surat anda telah diterima.',
            'status' => 'belum',
            'user_id_kap' => $user
        ]);

        // Kembalikan respons sukses
        return response()->json([
            'status' => true,
            'message' => 'Surat berhasil disetujui dan dipindahkan ke tabel yang sesuai.'
        ]);
    } catch (\Exception $e) {
        // Menangani error
        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

}
