<?php

namespace App\Http\Controllers;

use App\Models\t_approve_cetak_model;
use App\Models\t_pending_cetak_model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PermintaanSuratController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Permintaan Surat',
            'list' => ['Home', 'Permintaan Surat'],
        ];

        $page = (object)[
            'title' => 'Permintaan Surat',
        ];

        $activeMenu = 'surat';
        return view('surat.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list()
{
    try {

        $userId = Auth::id();
        $kaprodiProdiId = Auth::user()->detailKaprodi->prodi->prodi_id;

        // Query data t_pending_cetak_model dengan filter prodi_id sesuai Kaprodi
        $penerimaan = t_approve_cetak_model::select('t_pending_cetak_id', 'user_id', 'user_id_kap','pekerjaan_id', 'created_at', 'updated_at')
            ->with(['user', 'pekerjaan'])
            ->whereHas('user.detailMahasiswa.prodi', function ($query) use ($kaprodiProdiId) {
                $query->where('prodi_id', $kaprodiProdiId);
            });

        // Mengembalikan data ke DataTables
        return DataTables::of($penerimaan)
            ->addIndexColumn()
            ->addColumn('aksi', function ($penerimaan) {
                $btn = '<button onclick="approveUser(' . $penerimaan->t_pending_cetak_id . ')" class="btn btn-success btn-sm"><i class="fa-solid fa-check"></i></button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    }
}
}
