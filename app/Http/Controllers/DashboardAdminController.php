<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\detail_mahasiswaModel;
use App\Models\jamKompenModel;
use Illuminate\Support\Facades\Auth;  
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
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
            'title' => 'Dashboard Admin',
            'list' => ['Home', 'Dashboard Admin']
        ];

        // Menyusun menu aktif
        $activeMenu = 'dashboardADM';

        // Query untuk Total Mahasiswa
        $totalMahasiswa = DB::table('m_user')
            ->where('level_id', 3) // Level ID untuk mahasiswa
            ->count();

        // Mengirim data ke view dashboard mahasiswa
        return view('admin.dashboard', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'user' => $user,
            'jamkompen' => $jamkompen,
            'totalMahasiswa' => $totalMahasiswa
        ]);
    }
}
