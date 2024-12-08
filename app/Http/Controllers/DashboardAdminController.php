<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\detail_mahasiswaModel;
use App\Models\jamKompenModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function index()
    {
        // Data untuk chart dan dashboard
        $totalMahasiswa = DB::table('m_user')->where('level_id', 3)->count();
        $totalDosenTendik = DB::table('m_user')->where('level_id', 2)->count();
        $totalKaprodi = DB::table('m_user')->where('level_id', 4)->count();

        $totalPekerjaan = DB::table('pekerjaan')->count();

        $mahasiswaBelumKompen = DB::table('jam_kompen')
            ->where('akumulasi_jam', '>', 0)
            ->distinct('user_id')
            ->count();

        $mahasiswaSudahKompen = jamKompenModel::where('akumulasi_jam', '<=', 0)->count();

        // Menyusun breadcrumb
        $breadcrumb = (object)[
            'title' => 'Dashboard Admin',
            'list' => ['Home', 'Dashboard Admin']
        ];

        // Data untuk chart
        $chartData = [
            'labels' => ['Mahasiswa', 'Dosen/Tendik', 'Kaprodi', 'Pekerjaan', 'Belum Kompen', 'Sudah Kompen'], // Label yang unik
            'data' => [
                $totalMahasiswa,
                $totalDosenTendik,
                $totalKaprodi,
                $totalPekerjaan,
                $mahasiswaBelumKompen,
                $mahasiswaSudahKompen
            ],
        ];

        $activeMenu = 'dashboardAdm';

        // Mengirim data ke view
        return view('admin.dashboard', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'totalMahasiswa' => $totalMahasiswa,
            'totalDosenTendik' => $totalDosenTendik,
            'totalKaprodi' => $totalKaprodi,
            'totalPekerjaan' => $totalPekerjaan,
            'mahasiswaBelumKompen' => $mahasiswaBelumKompen,
            'mahasiswaSudahKompen' => $mahasiswaSudahKompen,
            'chartData' => $chartData
        ]);
    }
}