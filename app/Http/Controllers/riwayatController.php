<?php

namespace App\Http\Controllers;

use App\Models\ApprovePekerjaanModel;
use App\Models\PekerjaanModel;
use App\Models\PengumpulanModel;
use App\Models\ProgresModel;
use App\Models\t_approve_cetak_model;
use App\Models\t_pending_cetak_model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class riwayatController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Page Pengerjaan dan Riwayat Pekerjaan',
            'list' => ['Home', 'Riwayat'],
        ];

        $page = (object)[
            'title' => 'Page Pengerjaan dan Riwayat Pekerjaan',
        ];

        $activeMenu = 'riwayatMhs';

        $tugas = PekerjaanModel::whereHas('t_approve_pekerjaan', function ($query) {
            $query->where('user_id', Auth::id());
        })->with(['progres', 'progres.pengumpulan' => function ($query) {
            $query->where('user_id', Auth::id());
        }])->get();


        return view('riwayatMHS.index', [
            'activeMenu' => $activeMenu,
            'page' => $page,
            'breadcrumb' => $breadcrumb,
            'tugas' => $tugas,
        ]);
    }


    public function show_ajax($id)
    {
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan.persyaratan', 'detail_pekerjaan.kompetensiDosen.kompetensiAdmin')->where('pekerjaan_id', $id)->first();
        $jumlahProgres = ProgresModel::where('pekerjaan_id', $id)->count();

        return view('riwayatMHS.show_ajax', ['pekerjaan' => $pekerjaan, 'jumlahProgres' => $jumlahProgres, 'persyaratan' => $pekerjaan->detail_pekerjaan->persyaratan ?? collect(), 'kompetensi' => $pekerjaan->detail_pekerjaan->kompetensiDosen ?? collect()]);
    }

    public function enter_pekerjaan($id)
    {
        $breadcrumb = (object)[
            'title' => 'Pekerjaan',
            'list' => ['Home', 'Riwayat', 'Pekerjaan']
        ];

        $page = (object)[
            'title' => 'Pekerjaan'
        ];

        $activeMenu = 'riwayatMhs';
        $activeTab = 'progres';
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan', 'progres')->where('pekerjaan_id', $id)->first();
        return view('riwayatMHS.pekerjaan', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'activeTab' => $activeTab, 'pekerjaan' => $pekerjaan]);
    }

    public function enter_progres($id)
    {
        $breadcrumb = (object) [
            'title' => 'Progres',
            'list' => ['Home', 'Pekerjaan', 'Progres']
        ];

        $page = (object) [
            'title' => 'Page progres'
        ];

        $activeMenu = 'riwayatMhs';
        $progres = ProgresModel::with('pekerjaan', 'pekerjaan.detail_pekerjaan', 'pekerjaan.user', 'pengumpulan')->find($id);
        $pengumpulan = PengumpulanModel::where('user_id', Auth::id())->where('progres_id', $id)->first();


        return view('riwayatMHS.progres', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'progres' => $progres,
            'page' => $page,
            'pengumpulan' => $pengumpulan
        ]);
    }


    public function link_ajax($id)
    {
        $progres = ProgresModel::with('pekerjaan', 'pekerjaan.detail_pekerjaan', 'pekerjaan.user')->find($id);
        return view('riwayatMHS.link_ajax', ['progres' => $progres]);
    }

    public function gambar_ajax($id)
    {
        $progres = ProgresModel::with('pekerjaan', 'pekerjaan.detail_pekerjaan', 'pekerjaan.user')->find($id);
        return view('riwayatMHS.gambar_ajax', ['progres' => $progres]);
    }

    public function file_ajax($id)
    {
        $progres = ProgresModel::with('pekerjaan', 'pekerjaan.detail_pekerjaan', 'pekerjaan.user')->find($id);
        return view('riwayatMHS.file_ajax', ['progres' => $progres]);
    }

    public function store_link(Request $request)
    {
        $request->validate([
            'progres_id' => 'required|exists:progres,progres_id',
            'link' => 'required|url'
        ]);

        $progres = ProgresModel::findOrFail($request->progres_id);

        if ($progres->pengumpulan_id) {
            return response()->json([
                'status' => false,
                'message' => 'Pengumpulan sudah ada untuk progres ini.',
            ]);
        }

        if ($progres->deadline && \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($progres->deadline))) {
            return response()->json(['status' => false, 'message' => 'Aksi tidak diperbolehkan, deadline sudah terlewati'], 403);
        }



        $pengumpulan = new PengumpulanModel();
        $pengumpulan->progres_id = $request->progres_id;
        $pengumpulan->user_id = Auth::id();
        $pengumpulan->bukti_pengumpulan = $request->link;
        $pengumpulan->status = 'pending';
        $pengumpulan->save();


        return response()->json([
            'status' => true,
            'message' => 'Pengumpulan berhasil disimpan.',
        ]);
    }

    public function hapus_ajax($id)
    {
        $progres = ProgresModel::with('pekerjaan', 'pekerjaan.detail_pekerjaan', 'pekerjaan.user')->find($id);
        $pengumpulan = PengumpulanModel::with('progres')->where('user_id', Auth::id())->where('progres_id', $progres->progres_id)->first();

        return view('riwayatMHS.hapus_ajax', ['progres' => $progres, 'pengumpulan' => $pengumpulan]);
    }

    public function hapus(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $progres = ProgresModel::with('pengumpulan')->find($id);
            $pengumpulan = PengumpulanModel::with('progres')->where('progres_id', $id)->first();

            if ($progres && $pengumpulan) {
                try {
                    // Hapus file dari penyimpanan jika ada
                    if ($pengumpulan->bukti_pengumpulan && Storage::disk('public')->exists($pengumpulan->bukti_pengumpulan)) {
                        Storage::disk('public')->delete($pengumpulan->bukti_pengumpulan);
                    }

                    // Update pengumpulan_id di tabel progres menjadi null
                    // $progres->update(['pengumpulan_id' => null]);

                    // Hapus data pengumpulan
                    $pengumpulan->delete();

                    return response()->json([
                        'status' => true,
                        'message' => 'Data dan file berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
    }



    public function store_gambar(Request $request)
    {
        $request->validate([
            'progres_id' => 'required|exists:progres,progres_id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);



        $progres = ProgresModel::findorfail($request->progres_id);
        if ($progres->pengumpulan_id) {
            return response()->json([
                'status' => false,
                'message' => 'Pengumpulan sudah ada untuk progres ini.',
            ]);
        }

        if ($progres->deadline && \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($progres->deadline))) {
            return response()->json([
                'status' => false,
                'message' => 'Aksi tidak diperbolehkan, deadline sudah terlewati.',
            ], 403);
        }

        $namafile = $request->file('image')->getClientOriginalName();

        $gambar = $request->file('image')->store('pengumpulan_gambar', 'public');
        $pengumpulan = new PengumpulanModel();
        $pengumpulan->progres_id = $request->progres_id;
        $pengumpulan->user_id = Auth::id();
        $pengumpulan->bukti_pengumpulan = $gambar;
        $pengumpulan->namaoriginal = $namafile;
        $pengumpulan->status = 'pending';
        $pengumpulan->save();


        return response()->json([
            'status' => true,
            'message' => 'Pengumpulan berhasil disimpan.',
        ]);
    }

    public function store_file(Request $request)
    {
        $request->validate([
            'progres_id' => 'required|exists:progres,progres_id',
            'file' => 'required|mimes:pdf,xlsx,docx|max:2048'
        ]);

        $progres = ProgresModel::findorfail($request->progres_id);
        if ($progres->pengumpulan_id) {
            return response()->json([
                'status' => false,
                'message' => 'Pengumpulan sudah ada untuk progres ini.',
            ]);
        }

        if ($progres->deadline && \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($progres->deadline))) {
            return response()->json([
                'status' => false,
                'message' => 'Aksi tidak diperbolehkan, deadline sudah terlewati.',
            ], 403);
        }

        $namafile = $request->file('file')->getClientOriginalName();

        $file = $request->file('file')->store('pengumpulan_file', 'public');
        $pengumpulan = new PengumpulanModel();
        $pengumpulan->progres_id = $request->progres_id;
        $pengumpulan->user_id = Auth::id();
        $pengumpulan->bukti_pengumpulan = $file;
        $pengumpulan->namaoriginal = $namafile;
        $pengumpulan->status = 'pending';
        $pengumpulan->save();


        return response()->json([
            'status' => true,
            'message' => 'Pengumpulan berhasil disimpan.',
        ]);
    }
    public function request_cetak_surat(Request $request, $pekerjaan_id)
{
    try {
        $userId = Auth::id();

        // Cek apakah pekerjaan sudah melewati akumulasi_deadline
        $isDeadlineTerlewati = PekerjaanModel::where('pekerjaan_id', $pekerjaan_id)
            ->where('akumulasi_deadline', '<', now()) // Cek apakah akumulasi_deadline sudah lewat
            ->exists();

        // Validasi apakah user memiliki hak untuk mencetak surat
        $isValid = PekerjaanModel::where('pekerjaan_id', $pekerjaan_id)
            ->whereHas('progres.pengumpulan', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereIn('status', ['accept', 'decline']); // Status bisa accept atau decline
            })
            ->exists();

        // Jika deadline terlewati dan validasi status sudah terpenuhi
        if (!$isValid && !$isDeadlineTerlewati) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk membuat permintaan surat ini.');
        }

        // Cek apakah kombinasi pekerjaan_id dan user_id sudah ada
        $isExists = t_pending_cetak_model::where('user_id', $userId)
            ->where('pekerjaan_id', $pekerjaan_id)
            ->exists();

        if ($isExists) {
            return redirect()->back()->with('error', 'Permintaan cetak surat sudah pernah dibuat.');
        }

        $isExistsapprove = t_approve_cetak_model::where('user_id', $userId)
            ->where('pekerjaan_id', $pekerjaan_id)
            ->exists();

        if ($isExistsapprove) {
            return redirect()->back()->with('error', 'Permintaan cetak surat sudah pernah dibuat dan telah disetujui Kaprodi.');
        }

        // Buat data baru jika belum ada
        t_pending_cetak_model::create([
            'user_id' => $userId,
            'pekerjaan_id' => $pekerjaan_id,
        ]);

        ApprovePekerjaanModel::where('user_id', $userId)
        ->where('pekerjaan_id', $pekerjaan_id)
        ->update(['status' => 'selesai']);

        return redirect()->back()->with('success', 'Permintaan cetak surat berhasil dibuat.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

}
