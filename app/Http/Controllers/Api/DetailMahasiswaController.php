<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use app\Models\detail_mahasiswaModel;

class DetailMahasiswaController extends Controller
{
    // Mendapatkan detail mahasiswa dan periode berdasarkan user_id
    public function getDetailByUserId($user_id)
    {
        // Mengambil data detail mahasiswa dengan periode terkait
        $detailMahasiswa = detail_mahasiswaModel::with('periode')
            ->where('user_id', $user_id)
            ->first();

        if ($detailMahasiswa) {
            return response()->json($detailMahasiswa);
        } else {
            return response()->json(['message' => 'Detail Mahasiswa tidak ditemukan'], 404);
        }
    }
}
