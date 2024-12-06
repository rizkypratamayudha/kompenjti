<?php

namespace App\Http\Controllers;

use App\Models\t_pending_cetak_model;
use App\Models\t_approve_cetak_model;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardKaprodiController extends Controller
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

        // Menyusun breadcrumb
        $breadcrumb = (object)[
            'title' => 'Dashboard Kaprodi',
            'list' => ['Home', 'Dashboard Kaprodi']
        ];

        // Menyusun menu aktif
        $activeMenu = 'dashboardKap';

        // Data untuk chart
        $chartData = [
            'labels' => ['Pending Cetak', 'Approve Cetak'], // Label untuk Pie Chart
            'data' => [$pendingCount, $approveCount],       // Data chart
        ];

        // Mengirim data ke view dashboard Kaprodi
        return view('kaprodi.dashboard', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'user' => $user,
            'pendingCount' => $pendingCount,
            'approveCount' => $approveCount,
            'chartData' => $chartData
        ]);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
}