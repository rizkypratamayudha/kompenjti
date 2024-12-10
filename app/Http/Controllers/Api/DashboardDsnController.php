<?php  
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use App\Models\PekerjaanModel;
use App\Models\PendingPekerjaanModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardDsnController extends Controller
{
    public function index()
    {
        // Mengambil data user beserta relasi
        $user = UserModel::with([
            'level', // Relasi level
            'pekerjaan.detail_pekerjaan' // Relasi pekerjaan -> detail pekerjaan
        ])->where('user_id', Auth::id())->first();
    
        // Mengambil data pekerjaan berdasarkan user_id
        $pekerjaan = PekerjaanModel::with('user', 'detail_pekerjaan')  // Menggunakan relasi yang benar
            ->where('user_id', Auth::id())
            ->get();
    
        // Mengambil data t_pending_pekerjaan berdasarkan pekerjaan_id
        $pendingPekerjaan = PendingPekerjaanModel::with([
            'user.detailMahasiswa.prodi', // Menggunakan relasi yang diperlukan
            'pekerjaan' // Relasi pekerjaan terkait
        ])
        ->whereIn('pekerjaan_id', $pekerjaan->pluck('pekerjaan_id')) // Hanya pekerjaan yang terkait dengan user
        ->get();
    
        // Menghitung total pekerjaan berdasarkan user_id
        $totalPekerjaan = PekerjaanModel::where('user_id', Auth::id())->count();
    
        // Menghitung total pekerjaan berdasarkan status dan user_id
        $totalPekerjaanOpen = PekerjaanModel::where('user_id', Auth::id())
            ->where('status', 'open')
            ->count();
        $totalPekerjaanClosed = PekerjaanModel::where('user_id', Auth::id())
            ->where('status', 'closed')
            ->count();
    
        // Menyusun data yang akan dikirimkan ke response API
        return response()->json([
            'status' => true,
            'user' => $user,
            'pekerjaan' => $pekerjaan,
            'pending_pekerjaan' => $pendingPekerjaan,
            'total_pekerjaan' => $totalPekerjaan,
            'total_pekerjaan_open' => $totalPekerjaanOpen,
            'total_pekerjaan_closed' => $totalPekerjaanClosed,
        ]);
    }
}