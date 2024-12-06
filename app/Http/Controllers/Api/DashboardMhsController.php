<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\jamKompenModel;
use App\Models\detail_jamKompenModel;
use App\Models\MatkulModel;
use App\Models\PeriodeModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardMhsController extends Controller
{
    // Ambil data user beserta data terkait
    public function getUserInfo()
    {
        try {
            $user = UserModel::with([
                'level',
                'jamKompen.periode',
                'jamKompen.detail_jamKompen.matkul',
                'detailMahasiswa.prodi',
                'detailMahasiswa.periode'
            ])->where('user_id', Auth::id())->first();

            if (!$user) {
                return response()->json(['status' => 'not_found', 'message' => 'User tidak ditemukan.'], 404);
            }

            return response()->json(['status' => 'success', 'data' => $user], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching user info: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil data user.'], 500);
        }
    }

    // Ambil data jam kompen berdasarkan user dan periode
    public function getJamKompen(Request $request)
    {
        $request->validate(['periode_id' => 'required|integer']);

        try {
            $jamKompen = jamKompenModel::where('user_id', Auth::id())
                ->where('periode_id', $request->periode_id)
                ->first();

            if (!$jamKompen) {
                return response()->json(['status' => 'not_found', 'message' => 'Jam Kompen tidak ditemukan untuk periode ini.'], 404);
            }

            return response()->json(['status' => 'success', 'data' => $jamKompen], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching jam kompen: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil data jam kompen.'], 500);
        }
    }

    // Ambil detail jam kompen berdasarkan user dan periode
    public function getDetailJamKompen(Request $request)
    {
        $request->validate(['periode_id' => 'required|integer']);

        try {
            $jamKompen = jamKompenModel::where('user_id', Auth::id())
                ->where('periode_id', $request->periode_id)
                ->first();

            if (!$jamKompen) {
                return response()->json(['status' => 'not_found', 'message' => 'Jam Kompen tidak ditemukan untuk periode ini.'], 404);
            }

            $detailJamKompen = detail_jamKompenModel::where('jam_kompen_id', $jamKompen->jam_kompen_id)
                ->with('matkul')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'jam_kompen' => $jamKompen,
                    'detail_jam_kompen' => $detailJamKompen,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching detail jam kompen: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil detail jam kompen.'], 500);
        }
    }

    // Ambil data periode
    public function getPeriode()
    {
        try {
            $periode = PeriodeModel::select('periode_id', 'periode_nama')->get();
            return response()->json(['status' => 'success', 'data' => $periode], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching periode: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil data periode.'], 500);
        }
    }

    // Ambil data mata kuliah
    public function getMatkul()
    {
        try {
            $matkul = MatkulModel::select('matkul_id', 'matkul_nama')->get();
            return response()->json(['status' => 'success', 'data' => $matkul], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching mata kuliah: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil data mata kuliah.'], 500);
        }
    }
}