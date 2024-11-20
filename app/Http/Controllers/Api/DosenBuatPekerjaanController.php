<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PekerjaanModel;
use App\Models\detail_pekerjaanModel;
use Illuminate\Http\Request;
use App\Models\ProgresModel;
use App\Models\PersyaratanModel;
use carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        // Validasi input
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'jenis_pekerjaan' => 'required|string',
            'pekerjaan_nama' => 'required|string',
            'jumlah_anggota' => 'required|integer',
            'deskripsi_tugas' => 'required|string',
            'persyaratan' => 'required|array',
            'persyaratan.*' => 'required|string',
            'progress' => 'required|array',
            'progress.*.judul_progres' => 'required|string',
            'progress.*.jumlah_jam' => 'required|integer',
            'progress.*.jumlah_hari' => 'required|integer',
        ]);

        try {
            // Mulai proses transaksi
            DB::beginTransaction();

            // Insert data ke tabel pekerjaan
            $pekerjaan = PekerjaanModel::create([
                'user_id' => $validated['user_id'],
                'jenis_pekerjaan' => $validated['jenis_pekerjaan'],
                'pekerjaan_nama' => $validated['pekerjaan_nama'],
                'jumlah_jam_kompen' => array_sum(array_column($validated['progress'], 'jumlah_jam')),
                'status' => 'open',
                'akumulasi_deadline' => Carbon::now()->addDays(array_sum(array_column($validated['progress'], 'jumlah_hari'))),
            ]);

            // Insert data ke tabel detail_pekerjaan
            $detailPekerjaan = detail_pekerjaanModel::create([
                'pekerjaan_id' => $pekerjaan->pekerjaan_id,
                'jumlah_anggota' => $validated['jumlah_anggota'],
                'deskripsi_tugas' => $validated['deskripsi_tugas'],
            ]);

            // Insert data ke tabel persyaratan
            foreach ($validated['persyaratan'] as $persyaratanNama) {
                PersyaratanModel::create([
                    'detail_pekerjaan_id' => $detailPekerjaan->detail_pekerjaan_id, // Ambil ID dari tabel detail_pekerjaan
                    'persyaratan_nama' => $persyaratanNama,
                ]);
            }

            // Insert data ke tabel progress
            $currentDeadline = Carbon::now(); // Tanggal awal dari created_at
            foreach ($validated['progress'] as $progressItem) {
                $currentDeadline = $currentDeadline->copy()->addDays($progressItem['jumlah_hari']); // Update deadline

                ProgresModel::create([
                    'pekerjaan_id' => $pekerjaan->pekerjaan_id,
                    'judul_progres' => $progressItem['judul_progres'],
                    'jam_kompen' => $progressItem['jumlah_jam'],
                    'hari' => $progressItem['jumlah_hari'],
                    'deadline' => $currentDeadline,
                ]);
            }

            // Commit transaksi
            DB::commit();

            return response()->json(['message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();

            return response()->json([
                'message' => 'Data gagal disimpan',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }
}
