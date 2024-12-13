<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\notifikasiModel;
use App\Models\PengumpulanModel;
use App\Models\t_approve_cetak_model;
use App\Models\t_pending_cetak_model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
    public function indexmhs($login)
    {
        try {
            // Mendapatkan prodi_id dari Kaprodi yang sedang login
            $user = Auth::id();
            if ($login != $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
            // Query data t_pending_cetak_model dengan filter prodi_id sesuai Kaprodi
            $penerimaan = t_pending_cetak_model::with('user', 'pekerjaan')->where('user_id', $login)->get();

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
    public function indexmhssurat($login)
    {
        try {
            // Mendapatkan prodi_id dari Kaprodi yang sedang login
            $user = Auth::id();
            if ($login != $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
            // Query data t_pending_cetak_model dengan filter prodi_id sesuai Kaprodi
            $penerimaan = t_approve_cetak_model::with('user.detailMahasiswa.prodi', 'user.detailMahasiswa.periode', 'pekerjaan.user', 'kaprodi')->where('user_id', $login)->get();

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
            t_pending_cetak_model::where('user_id', $request->user_id)->where('pekerjaan_id', $request->pekerjaan_id)->delete();

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

    public function getQrUrl($id)
    {
        try {
            $user = Auth::id();

            // Retrieve the relevant data
            $penerimaan = t_approve_cetak_model::with('pekerjaan.progres', 'user', 'kaprodi')
                ->where('user_id', $user)
                ->where('t_approve_cetak_id', $id)
                ->firstOrFail();

            $pengumpulan = collect();

            foreach ($penerimaan->pekerjaan->progres as $progres) {
                $data = PengumpulanModel::with('user', 'progres')
                    ->where('user_id', $user)
                    ->where('progres_id', $progres->progres_id)
                    ->get();

                // Gabungkan hasil ke dalam koleksi utama
                $pengumpulan = $pengumpulan->merge($data);
            }

            // Generate the URL for the QR code
            $hash = hash('sha256', $penerimaan->t_approve_cetak_id);
            $url = URL::to('surat/download-pdf/' . $hash);

            // Define the path for saving the QR code
            $qrCodePath = public_path('storage/qrcodes/' . $penerimaan->t_approve_cetak_id . '.png');

            // Ensure the directory exists
            if (!file_exists(public_path('storage/qrcodes'))) {
                mkdir(public_path('storage/qrcodes'), 0777, true);
            }

            // Generate and save the QR code as a file
            $qrCodeBase = QrCode::size(150)->generate($url, $qrCodePath);

            // Return the public URL of the QR code
            $qrCodeUrl = asset('storage/qrcodes/' . $penerimaan->t_approve_cetak_id . '.png');

            return response()->json(['qrCodeUrl' => $url, 'pengumpulan' => $pengumpulan, 'penerimaan' => $penerimaan], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    public function riwayat(){
        try{
            $userId = Auth::id();

            // Mendapatkan prodi_id dari Kaprodi yang sedang login
            $kaprodiProdiId = Auth::user()->detailKaprodi->prodi->prodi_id;

            // Query data t_pending_cetak_model dengan filter prodi_id sesuai Kaprodi
            $penerimaan = t_approve_cetak_model::with('user','pekerjaan','kaprodi')
            ->whereHas('user.detailMahasiswa.prodi', function ($query) use ($kaprodiProdiId) {
                $query->where('prodi_id', $kaprodiProdiId);
            })->get();


            return response()->json([
                'success' => true,
                'data' => $penerimaan // Jika tidak ada data, data akan kosong, tapi tetap sukses
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

}
