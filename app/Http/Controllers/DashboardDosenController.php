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
        // Ambil data pekerjaan beserta relasi
        $pekerjaan = PekerjaanModel::with([
            'detail_pekerjaan.persyaratan', 
            'detail_pekerjaan.kompetensiDosen.kompetensiAdmin'
        ])->where('pekerjaan_id', $id)->first();
    
        if (!$pekerjaan) {
            return response()->json(['error' => 'Pekerjaan not found'], 404);
        }
    
        // Pastikan mengambil hanya satu detail pekerjaan
        $detailPekerjaan = $pekerjaan->detail_pekerjaan->first();
    
        // Jika tidak ada detail pekerjaan, pastikan data tetap valid
        $persyaratan = $detailPekerjaan ? $detailPekerjaan->persyaratan : collect();
        $kompetensi = $detailPekerjaan ? $detailPekerjaan->kompetensiDosen : collect();
        $jumlahProgres = ProgresModel::where('pekerjaan_id', $id)->count();
    
        return view('dosen.show_ajax', [
            'pekerjaan' => $pekerjaan,
            'jumlahProgres' => $jumlahProgres,
            'detailPekerjaan' => $detailPekerjaan,
            'persyaratan' => $persyaratan,
            'kompetensi' => $kompetensi,
        ]);
    }
    
    
}