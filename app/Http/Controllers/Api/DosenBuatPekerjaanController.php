<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PekerjaanModel;
use App\Models\detail_pekerjaanModel;
use Illuminate\Http\Request;
use App\Models\ProgresModel;
use App\Models\PersyaratanModel;
use App\Models\kompetensi_dosenModel;
use App\Models\kompetensi_adminModel;
use carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DosenBuatPekerjaanController extends Controller
{
    public function index($user_id)
    {
        // Mengambil data pekerjaan berdasarkan user_id
        $pekerjaan = PekerjaanModel::where('user_id', $user_id)->orderBy('created_at','desc')->get();

        // Menambahkan jumlah anggota dari tabel detail_pekerjaan
        $pekerjaanData = $pekerjaan->map(function ($item) {
            $detailPekerjaan = detail_pekerjaanModel::where('pekerjaan_id', $item->pekerjaan_id)->first();
            return [
                'pekerjaan_id' => $item->pekerjaan_id,
                'pekerjaan_nama' => $item->pekerjaan_nama,
                'jumlah_anggota' => $detailPekerjaan ? $detailPekerjaan->jumlah_anggota : 0, // Nilai default 0 jika tidak ada data
                'akumulasi_deadline' => $item->akumulasi_deadline,
                'status' => $item->status,
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
            'persyaratan' => 'nullable|array', // Persyaratan menjadi opsional
            'persyaratan.*' => 'nullable|string',
            'progress' => 'required|array',
            'progress.*.judul_progres' => 'required|string',
            'progress.*.jumlah_jam' => 'required|integer',
            'progress.*.jumlah_hari' => 'required|integer',
            'kompetensi_admin_id' => 'nullable|array', // Kompetensi dosen menjadi opsional
            'kompetensi_admin_id.*' => 'nullable|integer',
        ]);

        try {
            // Mulai proses transaksi
            DB::beginTransaction();

            // Insert data ke tabel pekerjaan
            $jumlahJamKompen = array_sum(array_column($validated['progress'], 'jumlah_jam'));

            $pekerjaan = PekerjaanModel::create([
                'user_id' => $validated['user_id'],
                'jenis_pekerjaan' => $validated['jenis_pekerjaan'],
                'pekerjaan_nama' => $validated['pekerjaan_nama'],
                'jumlah_jam_kompen' => $jumlahJamKompen,
                'status' => 'open',
                'akumulasi_deadline' => null,
            ]);

            // Insert data ke tabel detail_pekerjaan
            $detailPekerjaan = detail_pekerjaanModel::create([
                'pekerjaan_id' => $pekerjaan->pekerjaan_id,
                'jumlah_anggota' => $validated['jumlah_anggota'],
                'deskripsi_tugas' => $validated['deskripsi_tugas'],
            ]);

            // Insert data ke tabel persyaratan jika ada
            if (!empty($validated['persyaratan'])) {
                foreach ($validated['persyaratan'] as $persyaratanNama) {
                    PersyaratanModel::create([
                        'detail_pekerjaan_id' => $detailPekerjaan->detail_pekerjaan_id,
                        'persyaratan_nama' => $persyaratanNama,
                    ]);
                }
            }

            // Insert data ke tabel progress
            foreach ($validated['progress'] as $progressItem) {
                ProgresModel::create([
                    'pekerjaan_id' => $pekerjaan->pekerjaan_id,
                    'judul_progres' => $progressItem['judul_progres'],
                    'jam_kompen' => $progressItem['jumlah_jam'],
                    'hari' => $progressItem['jumlah_hari'],
                    'deadline' => null,
                ]);
            }

            /// Insert data ke tabel kompetensi_dosen jika ada
            if (!empty($validated['kompetensi_admin_id'])) {
                $kompetensiData = [];
                foreach ($validated['kompetensi_admin_id'] as $kompetensiAdminId) {
                    $kompetensiData[] = [
                        'detail_pekerjaan_id' => $detailPekerjaan->detail_pekerjaan_id,
                        'kompetensi_admin_id' => $kompetensiAdminId,
                    ];
                }
                kompetensi_dosenModel::insert($kompetensiData);
            }

            // Commit transaksi
            DB::commit();

            return response()->json([
                'message' => 'Data berhasil disimpan',
                'jumlah_jam_kompen' => $jumlahJamKompen,
                'akumulasi_deadline' => null,
            ], 200);
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

    public function getAllKompetensiAdmin()
    {
        try {
            $kompetensiAdmin = kompetensi_adminModel::select('kompetensi_admin_id', 'kompetensi_nama')->get();

            return response()->json([
                'message' => 'Data kompetensi admin berhasil diambil',
                'data' => $kompetensiAdmin,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data kompetensi admin',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $pekerjaanId)
    {
        $validatedData = $request->validate([
            'jenis_pekerjaan' => 'nullable|string',
            'pekerjaan_nama' => 'nullable|string',
            'jumlah_anggota' => 'nullable|integer',
            'deskripsi_tugas' => 'nullable|string',
            'persyaratan' => 'nullable|array',
            'persyaratan.*.persyaratan_nama' => 'nullable|string',
            'persyaratan.*.persyaratan_id' => 'nullable|integer',
            'progres' => 'nullable|array',
            'progres.*.judul_progres' => 'nullable|string',
            'progres.*.jam_kompen' => 'nullable|integer',
            'progres.*.hari' => 'nullable|integer',
            'progres.*.progres_id' => 'nullable|integer',
            'kompetensi' => 'nullable|array',
            'kompetensi.*.kompetensi_dosen_id' => 'nullable|integer',
            'kompetensi.*.kompetensi_admin_id' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            // Update data pekerjaan jika ada
            $pekerjaan = PekerjaanModel::findOrFail($pekerjaanId);
            $pekerjaan->update(array_filter([
                'jenis_pekerjaan' => $validatedData['jenis_pekerjaan'] ?? null,
                'pekerjaan_nama' => $validatedData['pekerjaan_nama'] ?? null,
            ]));

            // Update detail pekerjaan jika ada
            $detailPekerjaan = detail_pekerjaanModel::where('pekerjaan_id', $pekerjaanId)->firstOrFail();
            $detailPekerjaan->update(array_filter([
                'jumlah_anggota' => $validatedData['jumlah_anggota'] ?? null,
                'deskripsi_tugas' => $validatedData['deskripsi_tugas'] ?? null,
            ]));

            // Proses persyaratan jika ada
            if (isset($validatedData['persyaratan'])) {
                foreach ($validatedData['persyaratan'] as $persyaratanData) {
                    if (isset($persyaratanData['persyaratan_id'])) {
                        $persyaratan = PersyaratanModel::findOrFail($persyaratanData['persyaratan_id']);
                        $persyaratan->update(array_filter([
                            'persyaratan_nama' => $persyaratanData['persyaratan_nama'] ?? null,
                        ]));
                    } else {
                        PersyaratanModel::create([
                            'detail_pekerjaan_id' => $detailPekerjaan->detail_pekerjaan_id,
                            'persyaratan_nama' => $persyaratanData['persyaratan_nama'] ?? null,
                        ]);
                    }
                }
            }

            // Proses kompetensi jika ada
            if (isset($validatedData['kompetensi'])) {
                foreach ($validatedData['kompetensi'] as $kompetensiData) {
                    if (isset($kompetensiData['kompetensi_dosen_id'])) {
                        $kompetensi = kompetensi_dosenModel::where('kompetensi_dosen_id', $kompetensiData['kompetensi_dosen_id'])
                            ->where('detail_pekerjaan_id', $detailPekerjaan->detail_pekerjaan_id)
                            ->firstOrFail();
                        $kompetensi->update(array_filter([
                            'kompetensi_admin_id' => $kompetensiData['kompetensi_admin_id'] ?? null,
                        ]));
                    } else {
                        kompetensi_dosenModel::create([
                            'detail_pekerjaan_id' => $detailPekerjaan->detail_pekerjaan_id,
                            'kompetensi_admin_id' => $kompetensiData['kompetensi_admin_id'] ?? null,
                        ]);
                    }
                }
            }

            // Proses progres jika ada
            if (isset($validatedData['progres'])) {
                foreach ($validatedData['progres'] as $progresData) {
                    if (isset($progresData['progres_id'])) {
                        $progres = ProgresModel::findOrFail($progresData['progres_id']);
                        $progres->update(array_filter([
                            'judul_progres' => $progresData['judul_progres'] ?? null,
                            'jam_kompen' => $progresData['jam_kompen'] ?? null,
                            'hari' => $progresData['hari'] ?? null,
                        ]));
                    } else {
                        ProgresModel::create([
                            'pekerjaan_id' => $pekerjaanId,
                            'judul_progres' => $progresData['judul_progres'] ?? null,
                            'jam_kompen' => $progresData['jam_kompen'] ?? null,
                            'hari' => $progresData['hari'] ?? null,
                        ]);
                    }
                }
            }

            // Update total jam_kompen
            $totalJamKompen = ProgresModel::where('pekerjaan_id', $pekerjaanId)->sum('jam_kompen');
            $pekerjaan->update(['jumlah_jam_kompen' => $totalJamKompen]);

            DB::commit();

            return response()->json(['message' => 'Pekerjaan berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }


    public function deletePersyaratan($pekerjaanId, $persyaratanId)
    {
        try {
            // Cari detail_pekerjaan_id berdasarkan pekerjaan_id
            $detailPekerjaanId = detail_pekerjaanModel::where('pekerjaan_id', $pekerjaanId)->value('detail_pekerjaan_id');

            if (!$detailPekerjaanId) {
                throw new \Exception("Detail pekerjaan tidak ditemukan untuk pekerjaan_id $pekerjaanId");
            }

            // Cari dan hapus persyaratan berdasarkan persyaratan_id dan detail_pekerjaan_id
            $persyaratan = PersyaratanModel::where('persyaratan_id', $persyaratanId)
                ->where('detail_pekerjaan_id', $detailPekerjaanId)
                ->firstOrFail();

            $persyaratan->delete();

            return response()->json(['message' => 'Persyaratan berhasil dihapus'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Persyaratan tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function deleteKompetensi($pekerjaanId, $kompetensiDosenId)
    {
        try {
            // Cari detail_pekerjaan_id berdasarkan pekerjaan_id
            $detailPekerjaanId = detail_pekerjaanModel::where('pekerjaan_id', $pekerjaanId)->value('detail_pekerjaan_id');

            if (!$detailPekerjaanId) {
                throw new \Exception("Detail pekerjaan tidak ditemukan untuk pekerjaan_id $pekerjaanId");
            }

            // Temukan dan hapus kompetensi berdasarkan kompetensi_dosen_id dan detail_pekerjaan_id
            $kompetensi = kompetensi_dosenModel::where('kompetensi_dosen_id', $kompetensiDosenId)
                ->where('detail_pekerjaan_id', $detailPekerjaanId)
                ->firstOrFail();

            $kompetensi->delete();

            return response()->json(['message' => 'Kompetensi berhasil dihapus'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Kompetensi tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function deleteProgres($pekerjaanId, $progresId)
    {
        try {
            // Temukan dan hapus progres
            $progres = ProgresModel::where('progres_id', $progresId)
                ->where('pekerjaan_id', $pekerjaanId)
                ->firstOrFail();

            // Simpan jumlah jam kompen progres yang dihapus
            $jamKompenDihapus = $progres->jam_kompen;

            // Hapus progres
            $progres->delete();

            // Hitung ulang jumlah jam kompen total pada pekerjaan
            $totalJamKompen = ProgresModel::where('pekerjaan_id', $pekerjaanId)->sum('jam_kompen');

            // Update jumlah_jam_kompen di tabel pekerjaan
            $pekerjaan = PekerjaanModel::findOrFail($pekerjaanId);
            $pekerjaan->update(['jumlah_jam_kompen' => $totalJamKompen]);

            return response()->json(['message' => 'Progres berhasil dihapus dan jumlah jam kompen diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function startDeadline(Request $request, $pekerjaan_id)
    {
        try {
            $pekerjaan = PekerjaanModel::findOrFail($pekerjaan_id);

            // Validasi input
            $validatedData = $request->validate([
                'akumulasi_deadline' => 'required|date',
                'progres_deadlines' => 'required|array',
                'progres_deadlines.*.progress_id' => 'required|integer|exists:progres,progres_id',
                'progres_deadlines.*.deadline' => 'required|date',
            ]);

            // Update setiap progres dengan deadline baru
            foreach ($validatedData['progres_deadlines'] as $progresData) {
                $progres = ProgresModel::findOrFail($progresData['progress_id']);
                $progres->update(['deadline' => $progresData['deadline']]);
            }

            // Update akumulasi_deadline pada pekerjaan
            $pekerjaan->update(['akumulasi_deadline' => $validatedData['akumulasi_deadline']]);

            return response()->json([
                'message' => 'Deadline telah diperbarui',
                'akumulasi_deadline' => $validatedData['akumulasi_deadline']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui deadline',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function updateDeadline(Request $request, $pekerjaan_id)
    {
        try {
            $pekerjaan = PekerjaanModel::findOrFail($pekerjaan_id);

            // Validasi input
            $validatedData = $request->validate([
                'akumulasi_deadline' => 'required|date',
                'progres_deadlines' => 'required|array',
                'progres_deadlines.*.progress_id' => 'required|integer|exists:progres,progres_id',
                'progres_deadlines.*.deadline' => 'required|date',
                'progres_deadlines.*.hari' => 'nullable|integer|min:0', // Hari lama
                'progres_deadlines.*.tambah_hari' => 'nullable|integer|min:0', // Tambah hari
            ]);

            // Update setiap progres dengan deadline dan jumlah hari baru
            foreach ($validatedData['progres_deadlines'] as $progresData) {
                $progres = ProgresModel::findOrFail($progresData['progress_id']);

                // Hitung jumlah hari baru: hari lama + tambah hari
                $jumlahHariBaru = $progres->hari + ($progresData['tambah_hari'] ?? 0);

                // Update progres dengan total hari baru
                $progres->update([
                    'deadline' => $progresData['deadline'],
                    'hari' => $jumlahHariBaru,
                ]);
            }

            // Update akumulasi_deadline pada pekerjaan
            $pekerjaan->update(['akumulasi_deadline' => $validatedData['akumulasi_deadline']]);

            return response()->json([
                'message' => 'Deadline dan jumlah hari telah diperbarui',
                'akumulasi_deadline' => $validatedData['akumulasi_deadline']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui deadline dan jumlah hari',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function getProgressByPekerjaan($pekerjaan_id)
    {
        try {
            // Ambil data progres berdasarkan pekerjaan_id
            $progressList = ProgresModel::where('pekerjaan_id', $pekerjaan_id)->get();

            if ($progressList->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada progress yang ditemukan untuk pekerjaan ini.',
                    'progress' => []
                ], 404);
            }

            return response()->json([
                'message' => 'Progress ditemukan',
                'progress' => $progressList
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data progress',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function getPekerjaanDetail($id)
    {
        try {
            // Ambil data pekerjaan berdasarkan id
            $pekerjaan = PekerjaanModel::findOrFail($id);

            // Ambil detail pekerjaan terkait
            $detailPekerjaan = detail_pekerjaanModel::where('pekerjaan_id', $id)->first();

            if (!$detailPekerjaan) {
                return response()->json([
                    'message' => 'Detail pekerjaan tidak ditemukan',
                ], 404);
            }

            // Ambil persyaratan terkait
            $persyaratan = PersyaratanModel::where('detail_pekerjaan_id', $detailPekerjaan->detail_pekerjaan_id)->get();

            // Ambil progres terkait
            $progressList = ProgresModel::where('pekerjaan_id', $id)->get();

            // Ambil kompetensi dosen terkait
            $kompetensiDosen = kompetensi_dosenModel::where('detail_pekerjaan_id', $detailPekerjaan->detail_pekerjaan_id)
                ->join('kompetensi_admin', 'kompetensi_dosen.kompetensi_admin_id', '=', 'kompetensi_admin.kompetensi_admin_id')
                ->select('kompetensi_dosen.kompetensi_dosen_id', 'kompetensi_admin.kompetensi_nama', 'kompetensi_dosen.kompetensi_admin_id')
                ->get();

            return response()->json([
                'message' => 'Detail pekerjaan ditemukan',
                'pekerjaan' => $pekerjaan,
                'detail_pekerjaan' => $detailPekerjaan,
                'persyaratan' => $persyaratan,
                'progress' => $progressList,
                'kompetensi_dosen' => $kompetensiDosen,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data pekerjaan',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, $pekerjaanId)
    {
        $status = $request->input('status'); // Status baru (open atau closed)

        // Validasi input
        if (!in_array($status, ['open', 'close'])) {
            return response()->json(['message' => 'Status tidak valid'], 400);
        }

        // Cari pekerjaan berdasarkan ID
        $pekerjaan = PekerjaanModel::find($pekerjaanId);

        if (!$pekerjaan) {
            return response()->json(['message' => 'Pekerjaan tidak ditemukan'], 404);
        }

        // Update status pekerjaan
        $pekerjaan->status = $status;
        $pekerjaan->save();

        return response()->json([
            'message' => 'Status pekerjaan berhasil diperbarui',
            'status' => $status,
        ]);
    }

    public function delete($id)
    {
        try {
            // Mulai transaksi
            DB::beginTransaction();

            // Cari pekerjaan berdasarkan ID
            $pekerjaan = PekerjaanModel::findOrFail($id);

            // Hapus data terkait di tabel kompetensi_dosen
            $detailPekerjaan = detail_pekerjaanModel::where('pekerjaan_id', $pekerjaan->pekerjaan_id)->first();
            if ($detailPekerjaan) {
                kompetensi_dosenModel::where('detail_pekerjaan_id', $detailPekerjaan->detail_pekerjaan_id)->delete();

                // Hapus data terkait di tabel persyaratan
                PersyaratanModel::where('detail_pekerjaan_id', $detailPekerjaan->detail_pekerjaan_id)->delete();

                // Hapus data detail pekerjaan
                $detailPekerjaan->delete();
            }

            // Hapus data terkait di tabel progress
            ProgresModel::where('pekerjaan_id', $pekerjaan->pekerjaan_id)->delete();

            // Hapus pekerjaan
            $pekerjaan->delete();

            // Commit transaksi
            DB::commit();

            return response()->json([
                'message' => 'Pekerjaan berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus pekerjaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
