<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\notifikasiModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        try {
            $notifikasi = notifikasiModel::with('user', 'pekerjaan.user', 'kaprodi')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            // Pastikan nilai null diubah menjadi string default
            $notifikasi->transform(function ($item) {
                $item->user_id_kap = $item->user_id_kap ?? 'N/A';
                $item->status = $item->status ?? 'belum';
                return $item;
            });

            $response = [
                'success' => true,
                'message' => 'Notifikasi berhasil diambil.',
                'data' => ['notifikasi' => $notifikasi],
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function dibaca($id)
    {
        // Attempt to find the notification by its ID
        $notifikasi = NotifikasiModel::find($id);

        if ($notifikasi) {
            // Delete the notification
            $notifikasi->delete();

            // Return a success response with a message
            return response()->json([
                'status' => 'success',
                'message' => 'Notifikasi berhasil dihapus'
            ], 200);
        }

        // Return an error response if the notification is not found
        return response()->json([
            'status' => 'error',
            'message' => 'Notifikasi tidak ditemukan'
        ], 404);
    }

    public function hitung_notif_notifikasi(){
        $jumlah = notifikasiModel::where('user_id',Auth::id())->count();
        return ['jumlah' => $jumlah];
    }
}
