<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KompetensiModel;
use App\Models\detail_mahasiswaModel;
use App\Models\PeriodeModel;
use App\Models\kompetensi_adminModel;
use Illuminate\Support\Facades\DB;

class KompetensiController extends Controller
{
    public function index($user_id)
    {
        $kompetensi = KompetensiModel::join('kompetensi_admin', 'kompetensi.kompetensi_admin_id', '=', 'kompetensi_admin.kompetensi_admin_id')
            ->where('kompetensi.user_id', $user_id)
            ->get([
                'kompetensi.kompetensi_id',
                'kompetensi_admin.kompetensi_admin_id',
                'kompetensi_admin.kompetensi_nama',
                'kompetensi.pengalaman',
                'kompetensi.bukti',
            ]);

        $detailMahasiswa = detail_mahasiswaModel::with('periode')->where('user_id', $user_id)->first();
        $periodeNama = $detailMahasiswa ? $detailMahasiswa->periode->periode_nama : 'Periode tidak ditemukan';

        return response()->json([
            'periode' => $periodeNama,
            'kompetensi' => $kompetensi
        ]);
    }


    // Menyimpan data kompetensi baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'kompetensi_admin_id' => 'required|integer', // Menggunakan kompetensi_admin_id
            'pengalaman' => 'required|string',
            'bukti' => 'nullable|string',
        ]);

        $kompetensi = KompetensiModel::create($validatedData);
        return response()->json(['message' => 'Kompetensi berhasil disimpan', 'kompetensi' => $kompetensi]);
    }

    // Mendapatkan periode berdasarkan user_id
    public function getPeriodeByUserId($user_id)
    {
        $detailMahasiswa = detail_mahasiswaModel::where('user_id', $user_id)->first();

        if ($detailMahasiswa) {
            $periode = PeriodeModel::find($detailMahasiswa->periode_id);
            return response()->json([
                'periode_id' => $periode->periode_id,
                'periode_nama' => $periode->periode_nama,
            ]);
        }

        return response()->json(['message' => 'User periode not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'kompetensi_admin_id' => 'required|integer', // Menggunakan kompetensi_admin_id
            'pengalaman' => 'required|string',
            'bukti' => 'nullable|string',
        ]);

        $kompetensi = KompetensiModel::find($id);
        if ($kompetensi) {
            $kompetensi->update($validatedData);
            return response()->json(['message' => 'Data Kompetensi Anda Berhasil diperbaharui']);
        } else {
            return response()->json(['message' => 'Gagal memperbaharui Data Kompetensi'], 404);
        }
    }

    public function destroy($id)
    {
        $kompetensi = KompetensiModel::find($id);
        if ($kompetensi) {
            $kompetensi->delete();
            return response()->json(['message' => 'Data Kompetensi Anda Berhasil dihapus']);
        } else {
            return response()->json(['message' => 'Data Kompetensi tidak ditemukan'], 404);
        }
    }

    public function getKompetensiDetail($id)
    {
        $kompetensi = kompetensiModel::where('kompetensi_id', $id)
            ->with(['user', 'kompetensiAdmin', 'user.detailMahasiswa']) // Pastikan relasi detailMahasiswa didefinisikan
            ->first();

        if ($kompetensi) {
            return response()->json([
                'success' => true,
                'data' => [
                    'kompetensi_id' => $kompetensi->kompetensi_id,
                    'user_id' => $kompetensi->user_id ?? 0,
                    'detail_mahasiswa_id' => $kompetensi->user->detailMahasiswa->detail_mahasiswa_id ?? 0, // Ambil detail_mahasiswa_id
                    'periode' => $kompetensi->periode->periode_nama ?? 'Tidak Ada Periode',
                    'kompetensi_admin_id' => $kompetensi->kompetensi_admin_id ?? 0,
                    'kompetensi_nama' => $kompetensi->kompetensiAdmin->kompetensi_nama ?? 'Tidak Ada Nama Kompetensi',
                    'pengalaman' => $kompetensi->pengalaman ?? '',
                    'bukti' => $kompetensi->bukti ?? '',
                    'created_at' => $kompetensi->created_at ?? null,
                    'upload_at' => $kompetensi->upload_at ?? null,
                ],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kompetensi tidak ditemukan'
            ], 404);
        }
    }

    public function getKompetensiAdmin()
    {
        $kompetensiAdmin = kompetensi_adminModel::all();

        return response()->json($kompetensiAdmin);
    }

    public function getDetailMahasiswaIdByUserId($userId)
    {
        $detailMahasiswa = DB::table('detail_mahasiswa')
            ->join('m_user', 'detail_mahasiswa.user_id', '=', 'm_user.user_id') // Join ke tabel m_user
            ->where('detail_mahasiswa.user_id', $userId)
            ->select('detail_mahasiswa.detail_mahasiswa_id', 'm_user.nama', 'm_user.username') // Ambil detail_mahasiswa_id, nama, dan username
            ->first();

        if ($detailMahasiswa) {
            return response()->json([
                'success' => true,
                'data' => [
                    'detail_mahasiswa_id' => $detailMahasiswa->detail_mahasiswa_id,
                    'nama' => $detailMahasiswa->nama,
                    'username' => $detailMahasiswa->username
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Detail Mahasiswa ID not found'
            ], 404);
        }
    }
}
