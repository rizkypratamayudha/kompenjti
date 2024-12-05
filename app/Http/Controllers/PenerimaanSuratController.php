<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanModel;
use App\Models\ProdiModel;
use App\Models\ProgresModel;
use App\Models\t_approve_cetak_model;
use App\Models\t_pending_cetak_model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PenerimaanSuratController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Penerimaan Surat',
            'list' => ['Home', 'Penerimaan Surat'],
        ];

        $page = (object)[
            'title' => 'Penerimaan Surat',
        ];

        $activeMenu = 'penerimaan';
        return view('penerimaan.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu,]);
    }

    public function list()
{
    try {
        // Mendapatkan user yang sedang login
        $userId = Auth::id();
        // Mendapatkan prodi_id dari Kaprodi yang sedang login
        $kaprodiProdiId = Auth::user()->detailKaprodi->prodi->prodi_id;

        // Query data t_pending_cetak_model dengan filter prodi_id sesuai Kaprodi
        $penerimaan = t_pending_cetak_model::select('t_pending_cetak_id', 'user_id', 'pekerjaan_id', 'created_at', 'updated_at')
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

    public function approve($id)
{
    // Mencari data pending cetak berdasarkan ID
    $pendingCetak = t_pending_cetak_model::find($id);
    $user = Auth::id();
    if (!$pendingCetak) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Mengecek apakah sudah ada data dengan kombinasi user_id dan pekerjaan_id di t_approve_cetak_model
    $existingRecord = t_approve_cetak_model::where('user_id', $pendingCetak->user_id)
                                            ->where('pekerjaan_id', $pendingCetak->pekerjaan_id)
                                            ->first();

    // Jika data sudah ada, return error
    if ($existingRecord) {
        return response()->json(['error' => 'Surat ini sudah di acc'], 409);
    }

    // Jika data tidak ada, lanjutkan proses approval dan simpan ke t_approve_cetak_model
    $cetak = t_approve_cetak_model::create([
        'user_id' => $pendingCetak->user_id,
        'pekerjaan_id' => $pendingCetak->pekerjaan_id,
        'user_id_kap' =>  $user
    ]);

    // Hapus data dari t_pending_cetak_model setelah approval
    $pendingCetak->delete();

    return response()->json(['status' => true, 'message' => 'User approved and moved to respective detail table.']);
}

}
