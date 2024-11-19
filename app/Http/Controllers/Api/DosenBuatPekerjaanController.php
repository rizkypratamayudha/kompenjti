<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PekerjaanModel;
use App\Models\detail_pekerjaanModel;
use Illuminate\Http\Request;
use App\Models\ProgresModel;
use App\Models\PersyaratanModel;
use carbon\Carbon;

class DosenBuatPekerjaanController extends Controller
{
    public function index($user_id)
    {
        // Mengambil data pekerjaan berdasarkan user_id
        $pekerjaan = PekerjaanModel::where('user_id', $user_id)->get();

        // Menambahkan jumlah anggota dari tabel detail_pekerjaan
        $pekerjaanData = $pekerjaan->map(function ($item) {
            $detailPekerjaan = detail_pekerjaanModel::where('pekerjaan_id', $item->pekerjaan_id)->first();
            return [
                'pekerjaan_id' => $item->pekerjaan_id,
                'pekerjaan_nama' => $item->pekerjaan_nama,
                'jumlah_anggota' => $detailPekerjaan ? $detailPekerjaan->jumlah_anggota : 0, // Nilai default 0 jika tidak ada data
            ];
        });

        return response()->json([
            'message' => 'Pekerjaan ditemukan',
            'pekerjaan' => $pekerjaanData
        ]);
    }

    public function store(Request $request)
    {
        try {
            // Insert data ke tabel pekerjaan
            $pekerjaan = new PekerjaanModel();
            $pekerjaan->user_id = $request->user_id;
            $pekerjaan->jenis_pekerjaan = $request->jenis_pekerjaan;
            $pekerjaan->pekerjaan_nama = $request->pekerjaan_nama;
            $pekerjaan->jumlah_jam_kompen = array_sum(array_column($request->progress, 'jumlah_jam'));
            $pekerjaan->status = 'open';
            $pekerjaan->akumulasi_deadline = Carbon::parse($request->created_at)->addDays(array_sum(array_column($request->progress, 'jumlah_hari')));
            $pekerjaan->created_at = $request->created_at;
            $pekerjaan->save();

            // Insert data ke tabel detail_pekerjaan
            $detailPekerjaan = new detail_pekerjaanModel();
            $detailPekerjaan->pekerjaan_id = $pekerjaan->id;
            $detailPekerjaan->jumlah_anggota = $request->jumlah_anggota;
            $detailPekerjaan->deskripsi_tugas = $request->deskripsi_tugas;
            $detailPekerjaan->created_at = $request->created_at;
            $detailPekerjaan->save();

            // Insert data ke tabel persyaratan
            foreach ($request->persyaratan as $persyaratanNama) {
                $persyaratan = new PersyaratanModel();
                $persyaratan->detail_pekerjaan_id = $detailPekerjaan->id;
                $persyaratan->persyaratan_nama = $persyaratanNama;
                $persyaratan->save();
            }

            // Insert data ke tabel progress
            $deadline = Carbon::parse($request->created_at);
            foreach ($request->progress as $progressItem) {
                $deadline = $deadline->addDays($progressItem['jumlah_hari']);
                $progress = new ProgresModel();
                $progress->pekerjaan_id = $pekerjaan->id;
                $progress->judul_progress = $progressItem['judul_progress'];
                $progress->jam_kompen = $progressItem['jumlah_jam'];
                $progress->hari = $progressItem['jumlah_hari'];
                $progress->deadline = $deadline;
                $progress->created_at = $request->created_at;
                $progress->save();
            }

            return response()->json(['message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data gagal disimpan', 'error' => $e->getMessage()], 500);
        }
    }
}
