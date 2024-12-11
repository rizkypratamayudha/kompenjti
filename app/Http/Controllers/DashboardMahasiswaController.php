<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\detail_mahasiswaModel;
use App\Models\jamKompenModel;
use Illuminate\Support\Facades\Auth;  
class DashboardMahasiswaController extends Controller
{
    public function index() {
        $user = UserModel::with([
            'level',
            'jamKompen',
            'jamKompen.periode',
            'jamKompen.detail_jamKompen.matkul',
            'detailMahasiswa.prodi',
            'detailMahasiswa.periode'
        ])->where('user_id', Auth::id())->first();
        
        $jamkompen = jamKompenModel::with('user','detail_jamKompen')->where('user_id', Auth::id())->get();

        // Menyusun breadcrumb
        $breadcrumb = (object)[
            'title' => 'Dashboard Mahasiswa',
            'list' => ['Home', 'Dashboard Mahasiswa']
        ];

        // Menyusun menu aktif
        $activeMenu = 'dashboardMhs';

        // Mengirim data ke view dashboard mahasiswa
        return view('mahasiswa.dashboard', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'user' => $user,
            'jamkompen' => $jamkompen
        ]);
    }
}