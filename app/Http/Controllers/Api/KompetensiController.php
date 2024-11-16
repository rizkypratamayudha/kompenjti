<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KompetensiModel;
use App\Models\detail_mahasiswaModel;
use App\Models\PeriodeModel;

class KompetensiController extends Controller
{
    // Mengambil data kompetensi berdasarkan user_id
    public function index($user_id)
    {
        $kompetensi = KompetensiModel::where('user_id', $user_id)->get();

        // Menambahkan informasi periode dari detail mahasiswa
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
            'kompetensi_nama' => 'required|string',
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
            'kompetensi_nama' => 'required|string',
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
        // Gunakan model kompetensiModel dan perbaiki properti id
        $kompetensi = kompetensiModel::where('kompetensi_id', $id)
            ->with(['user']) // Pastikan relasi user didefinisikan di model
            ->first();

        if ($kompetensi) {
            return response()->json([
                'success' => true,
                'data' => $kompetensi
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kompetensi tidak ditemukan'
            ], 404);
        }
    }
}
