<?php

namespace App\Http\Controllers;

use App\Models\t_approve_cetak_model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class riwayatPermintaanSuratController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title' => 'Riwayat Penerimaan',
            'list' => ['Home','Riwayat Penerimaan'],
        ];

        $page = (object)[
            'title' => 'Riwayat Penerimaan',
        ];

        $activeMenu = 'riwayatPen';
        return view('riwayatPenerimaanKap.index',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu]);
    }

    public function list(){
        try {
            // Mendapatkan user yang sedang login
            $userId = Auth::id();
            // Mendapatkan prodi_id dari Kaprodi yang sedang login
            $kaprodiProdiId = Auth::user()->detailKaprodi->prodi->prodi_id;

            // Query data t_pending_cetak_model dengan filter prodi_id sesuai Kaprodi
            $penerimaan = t_approve_cetak_model::select('t_approve_cetak_id', 'user_id', 'pekerjaan_id', 'created_at', 'updated_at')
                ->with(['user', 'pekerjaan'])
                ->whereHas('user.detailMahasiswa.prodi', function ($query) use ($kaprodiProdiId) {
                    $query->where('prodi_id', $kaprodiProdiId);
                });

            // Mengembalikan data ke DataTables
            return DataTables::of($penerimaan)
                ->addIndexColumn()
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
