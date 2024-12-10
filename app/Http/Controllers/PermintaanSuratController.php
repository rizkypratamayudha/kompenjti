<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanModel;
use App\Models\t_approve_cetak_model;
use App\Models\t_pending_cetak_model;
use BaconQrCode\Encoder\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode as FacadesQrCode;
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
            // Ambil ID pengguna yang sedang login
            $userId = Auth::id();

            // Query data t_pending_cetak_model dengan filter user_id sesuai pengguna yang sedang login
            $penerimaan = t_approve_cetak_model::select('t_approve_cetak_id', 'user_id', 'pekerjaan_id', 'user_id_kap', 'created_at', 'updated_at')
                ->with(['user', 'pekerjaan', 'kaprodi']) // Mengambil relasi user dan pekerjaan
                ->where('user_id', $userId); // Filter berdasarkan user_id yang sedang login

            // Mengembalikan data ke DataTables
            return DataTables::of($penerimaan)
                ->addIndexColumn()
                ->addColumn('aksi', function ($penerimaan) {
                    // Tombol aksi (misalnya tombol approve)
                    $btn = '<a href="surat/download-pdf/' . $penerimaan->t_approve_cetak_id . '" class="btn btn-success btn-sm"><i class="fa-solid fa-download"> </i> Download</a> ';
                    return $btn;
                })
                ->rawColumns(['aksi']) // Pastikan kolom aksi mendukung HTML
                ->make(true);
        } catch (\Exception $e) {
            // Menangani error dan mengembalikan response JSON
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function export_pdf($id)
    {
        try {
            $user = Auth::id();
            $penerimaan = t_approve_cetak_model::select(
                't_approve_cetak_id',
                'user_id',
                'pekerjaan_id',
                'user_id_kap',
                'created_at',
                'updated_at'
            )
                ->with('user', 'pekerjaan.progres', 'kaprodi',)
                ->where('user_id', $user)
                ->where('t_approve_cetak_id', $id)
                ->firstOrFail();

            $pengumpulan = collect();

            foreach ($penerimaan->pekerjaan->progres as $progres) {
                $data = PengumpulanModel::with('user', 'progres')
                    ->where('user_id', $user)
                    ->where('progres_id', $progres->progres_id)
                    ->get();

                // Gabungkan hasil ke dalam koleksi utama
                $pengumpulan = $pengumpulan->merge($data);
            }

            $hash = hash('sha256',$penerimaan->t_approve_cetak_id);

            $url = url('surat/download-pdf/' . $hash );

            $qrCodePath = public_path('storage/qrcodes/' . $penerimaan->t_approve_cetak_id . '.png');

            if (!file_exists(public_path('storage/qrcodes'))) {
                mkdir(public_path('storage/qrcodes'), 0777, true);
            }


            $qrCodeBase = FacadesQrCode::size(150)->generate($url, $qrCodePath);
            // Generate PDF
            $pdf = Pdf::loadView('surat.export_pdf', ['penerimaan' => $penerimaan, 'pengumpulan' => $pengumpulan,'qrcode'=> asset('storage/qrcodes' .$penerimaan->t_approve_cetak_id. '.png')]);
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption("isRemoteEnabled", true);

            // Stream atau download file PDF
            return $pdf->stream('Surat Bukti Kompen ' .$penerimaan->user->nama . ' ' . date('Y-m-d H:i:s') . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function export_pdf_scan($hash){
        try{
            $user = Auth::id();
            $penerimaan = t_approve_cetak_model::all()->first(function ($item) use ($hash) {
                return hash('sha256', $item->t_approve_cetak_id) === $hash;
            });

            if (!$penerimaan) {
                return response()->json(['error' => 'Hash tidak valid'], 404);
            }

            $pengumpulan = collect();

            foreach ($penerimaan->pekerjaan->progres as $progres) {
                $data = PengumpulanModel::with('user', 'progres')
                    ->where('user_id',$user)
                    ->where('progres_id', $progres->progres_id)
                    ->get();

                // Gabungkan hasil ke dalam koleksi utama
                $pengumpulan = $pengumpulan->merge($data);
            }

            $pdf = Pdf::loadView('surat.export_pdf_scan', ['penerimaan' => $penerimaan, 'pengumpulan' => $pengumpulan,]);
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption("isRemoteEnabled", true);

            // Stream atau download file PDF
            return $pdf->stream('Surat Bukti Kompen ' .$penerimaan->user->nama . ' ' . date('Y-m-d H:i:s') . '.pdf');
        } catch (\Exception $e){
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
