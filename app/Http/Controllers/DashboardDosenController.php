<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\detail_pekerjaanModel;
use App\Models\kompetensi_adminModel;
use App\Models\UserModel;
use App\Models\PekerjaanModel;
use App\Models\ProgresModel;
use Illuminate\Support\Facades\Auth;

class DashboardDosenController extends Controller
{
    public function index()
    {
        // Mengambil data user beserta relasi
        $user = UserModel::with([
            'level', // Relasi level
            'detailDosen.pekerjaan.detail_pekerjaan' // Relasi detail dosen -> pekerjaan -> detail pekerjaan
        ])->where('user_id', Auth::id())->first();

        // Mengambil data pekerjaan berdasarkan user_ipd
        $pekerjaan = PekerjaanModel::with('user', 'detail_pekerjaan')
            ->where('user_id', Auth::id())
            ->get();

        // Menyusun breadcrumb
        $breadcrumb = (object)[
            'title' => 'Dashboard Dosen',
            'list' => ['Home', 'Dashboard Dosen']
        ];

        // Menyusun menu aktif
        $activeMenu = 'dashboardDos';

        // Mengirim data ke view dashboard dosen
        return view('dosen.dashboard', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'user' => $user,
            'pekerjaan' => $pekerjaan
        ]);
    }
    public function show_ajax($id)
    {
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan.persyaratan', 'detail_pekerjaan.kompetensiDosen.kompetensiAdmin')->where('pekerjaan_id', $id)->first();
        $jumlahProgres = ProgresModel::where('pekerjaan_id', $id)->count();

        return view('dosen.show_ajax', [
            'pekerjaan' => $pekerjaan,
            'jumlahProgres' => $jumlahProgres,
            'persyaratan' => $pekerjaan->detail_pekerjaan->persyaratan ?? collect(),
            'kompetensi' => $pekerjaan->detail_pekerjaan->kompetensiDosen ?? collect(),
        ]);
    }
}
