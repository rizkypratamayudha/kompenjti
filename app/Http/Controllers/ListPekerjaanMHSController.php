<?php

namespace App\Http\Controllers;

use App\Models\ApprovePekerjaanModel;
use App\Models\PekerjaanModel;
use App\Models\PendingPekerjaanController;
use App\Models\PendingPekerjaanModel;
use App\Models\PengumpulanModel;
use App\Models\ProgresModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Prompts\Progress;

class ListPekerjaanMHSController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'List Pekerjaan',
            'list' => ['Home', 'List Pekerjaan']
        ];

        $page = (object)[
            'title' => 'List Pekerjaan yang tersedia'
        ];

        $activeMenu = 'pekerjaan';
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan', 'progres', 't_pending_pekerjaan')->where('status', 'open')->get();

        return  view('pekerjaanMHS.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'tugas' => $pekerjaan]);
    }

    public function apply(Request $request)
    {
        // Validasi inputan
        $validator = Validator::make($request->all(), [
            'pekerjaan_id' => 'required|exists:pekerjaan,pekerjaan_id',
            'user_id' => 'required|exists:m_user,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }


        $existingApply = PendingPekerjaanModel::where('pekerjaan_id', $request->pekerjaan_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($existingApply) {
            return back()->with('error', 'Anda sudah melamar pekerjaan ini sebelumnya.');
        }

        $existingApplyapprove = ApprovePekerjaanModel::where('pekerjaan_id', $request->pekerjaan_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($existingApplyapprove) {
            return back()->with('error', 'Anda sudah melamar pekerjaan ini sebelumnya dan sudah masuk dalam pekerjaan');
        }

        // Jika belum ada, simpan data apply ke PendingPekerjaanModel
        PendingPekerjaanModel::create([
            'pekerjaan_id' => $request->pekerjaan_id,
            'user_id' => $request->user_id,
            'status' => 'belum',
        ]);

        return back()->with('success', 'Anda Telah Berhasil Melamar Pekerjaan');
    }

    public function cancelApply(Request $request)
    {
        $userId = Auth::id(); // Mendapatkan ID pengguna yang sedang login

        // Validasi inputan
        $validator = Validator::make($request->all(), [
            'pekerjaan_id' => 'required|exists:pekerjaan,pekerjaan_id', // Validasi pekerjaan ID
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Menghapus data lamaran berdasarkan user_id dan pekerjaan_id
        $deleted = PendingPekerjaanModel::where('user_id', $userId)
            ->where('pekerjaan_id', $request->pekerjaan_id)
            ->delete();

        if ($deleted) {
            return response()->json([
                'status' => true,
                'message' => 'Lamaran berhasil dibatalkan.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membatalkan lamaran. Data tidak ditemukan.'
            ], 404);
        }
    }
    public function checkIfApplied(Request $request)
    {
        $userId = Auth::id();
        $pekerjaanId = $request->pekerjaan_id;

        $isApplied = PendingPekerjaanModel::where('pekerjaan_id', $pekerjaanId)
            ->where('user_id', $userId)
            ->exists();

        $isApprove = ApprovePekerjaanModel::
            where('user_id', $userId)->where('status','belum')
            ->exists();

        // Mengembalikan status apply dan approve
        if ($isApplied) {
            return response()->json(['isApplied' => true, 'isApprove' => false]);
        } elseif ($isApprove) {
            return response()->json(['isApplied' => false, 'isApprove' => true]);
        }

        return response()->json(['isApplied' => false, 'isApprove' => false]);
    }




    public function get_anggota($id)
    {
        $anggotaJumlah = ApprovePekerjaanModel::where('pekerjaan_id', $id)->count();

        return response()->json([
            'status' => true,
            'anggotaJumlah' => $anggotaJumlah
        ]);
    }
}
