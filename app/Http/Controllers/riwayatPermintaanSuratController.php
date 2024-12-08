<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanModel;
use App\Models\t_approve_cetak_model;
use Barryvdh\DomPDF\Facade\Pdf;
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
                ->addColumn('aksi', function ($penerimaan) {
                    // Tombol aksi (misalnya tombol approve)
                    $btn = '<a href="riwayatPenerimaan/download-pdf/' . $penerimaan->t_approve_cetak_id . '" class="btn btn-success btn-sm"><i class="fa-solid fa-download"> </i> Download</a> ';
                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function export_pdf($id)
    {
        try {
            $penerimaan = t_approve_cetak_model::select(
                't_approve_cetak_id',
                'user_id',
                'pekerjaan_id',
                'user_id_kap',
                'created_at',
                'updated_at'
            )
                ->with('user', 'pekerjaan.progres', 'kaprodi',)
                ->where('t_approve_cetak_id', $id)
                ->firstOrFail();

            $pengumpulan = collect();

            foreach ($penerimaan->pekerjaan->progres as $progres) {
                $data = PengumpulanModel::with('user', 'progres')
                    ->where('user_id', $penerimaan->user_id)
                    ->where('progres_id', $progres->progres_id)
                    ->get();

                // Gabungkan hasil ke dalam koleksi utama
                $pengumpulan = $pengumpulan->merge($data);
            }
            // Generate PDF
            $pdf = Pdf::loadView('surat.export_pdf', ['penerimaan' => $penerimaan, 'pengumpulan' => $pengumpulan]);
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption("isRemoteEnabled", true);

            // Stream atau download file PDF
            return $pdf->stream('Surat Bukti Kompen ' .$penerimaan->user->nama . ' ' . date('Y-m-d H:i:s') . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
