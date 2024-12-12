<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\t_pending_cetak_model;
use App\Models\t_approve_cetak_model;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DashboardKapController extends Controller
{
      public function index()
    {
        try {
            // Mendapatkan user yang sedang login beserta detail Kaprodi
            $user = UserModel::with([
                'level',
                'detailKaprodi.prodi' // Relasi Kaprodi ke Prodi
            ])->where('user_id', Auth::id())->firstOrFail();

            // Mendapatkan prodi_id dari Kaprodi yang sedang login
            $kaprodiProdiId = $user->detailKaprodi->prodi->prodi_id;

            // Menghitung jumlah data t_pending_cetak berdasarkan kriteria
            $pendingCount = t_pending_cetak_model::whereHas('user.detailMahasiswa.prodi', function ($query) use ($kaprodiProdiId) {
                $query->where('prodi_id', $kaprodiProdiId);
            })->count();

            // Menghitung jumlah data t_approve_cetak berdasarkan prodi_id
            $approveCount = t_approve_cetak_model::whereHas('user.detailMahasiswa.prodi', function ($query) use ($kaprodiProdiId) {
                $query->where('prodi_id', $kaprodiProdiId);
            })->count();

            // Mengembalikan data dalam format JSON
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'pendingCount' => $pendingCount,
                    'approveCount' => $approveCount,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}