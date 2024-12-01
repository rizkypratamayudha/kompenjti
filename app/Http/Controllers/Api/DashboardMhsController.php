<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PeriodeModel;
use App\Models\jamKompenModel;
use App\Models\detail_jamKompenModel;
use App\Models\MatkulModel;
use Illuminate\Support\Facades\Log;

class DashboardMhsController extends Controller
{
    // Ambil data periode
    public function getPeriode()
    {
        try {
            $periode = PeriodeModel::select('periode_id', 'periode_nama')->get();
            return response()->json([
                'status' => 'success',
                'data' => $periode
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching periode: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data periode.'
            ], 500);
        }
    }

    public function getJamKompen(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'periode_id' => 'required|integer',
        ]);

        try {
            $jamKompen = jamKompenModel::where('user_id', $request->user_id)
                ->where('periode_id', $request->periode_id)
                ->first();

            if (!$jamKompen) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Jam Kompen tidak ditemukan untuk user dan periode ini.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $jamKompen
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching jam kompen: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data jam kompen.'
            ], 500);
        }
    }

    public function getDetailJamKompenByUserAndPeriode(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'periode_id' => 'required|integer',
        ]);

        try {
            $jamKompen = jamKompenModel::where('user_id', $request->user_id)
                ->where('periode_id', $request->periode_id)
                ->first();

            if (!$jamKompen) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Jam Kompen tidak ditemukan untuk user dan periode ini.'
                ], 404);
            }

            $detailJamKompen = detail_jamKompenModel::where('jam_kompen_id', $jamKompen->jam_kompen_id)
                ->join('matkul', 'detail_jamKompen.matkul_id', '=', 'matkul.matkul_id')
                ->select('detail_jamKompen.*', 'matkul.matkul_nama')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'jam_kompen' => $jamKompen,
                    'detail_jam_kompen' => $detailJamKompen
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching detail jam kompen: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail jam kompen.'
            ], 500);
        }
    }

    public function getMatkul()
    {
        try {
            $matkul = MatkulModel::select('matkul_id', 'matkul_nama')->get();
            return response()->json([
                'status' => 'success',
                'data' => $matkul
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching mata kuliah: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data mata kuliah.'
            ], 500);
        }
    }
}