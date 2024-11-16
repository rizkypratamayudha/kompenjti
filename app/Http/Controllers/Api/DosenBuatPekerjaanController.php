<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PekerjaanModel;
use App\Models\detail_pekerjaanModel;
use Illuminate\Http\Request;

class DosenBuatPekerjaanController extends Controller
{
    public function index($user_id)
    {
        // Mengambil data pekerjaan berdasarkan user_id
        $pekerjaan = PekerjaanModel::where('user_id', $user_id)->get();

        // Menambahkan jumlah anggota dari tabel detail_pekerjaan
        $pekerjaanData = $pekerjaan->map(function ($item) {
            $detailPekerjaan = detail_pekerjaanModel::where('pekerjaan_id', $item->pekerjaan_id)->first();
            return [
                'pekerjaan_id' => $item->pekerjaan_id,
                'pekerjaan_nama' => $item->pekerjaan_nama,
                'jumlah_anggota' => $detailPekerjaan ? $detailPekerjaan->jumlah_anggota : 0, // Nilai default 0 jika tidak ada data
            ];
        });

        return response()->json([
            'message' => 'Pekerjaan ditemukan',
            'pekerjaan' => $pekerjaanData
        ]);
    }
}