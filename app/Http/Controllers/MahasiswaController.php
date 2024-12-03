<?php

namespace App\Http\Controllers;

use App\Models\detail_jamKompenModel;
use App\Models\jamKompenModel;
use App\Models\MatkulModel;
use App\Models\PeriodeModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class MahasiswaController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title'=>'Data Mahasiswa Kompensasi',
            'list'=>['Home','Mahasiswa Kompensasi']
        ];

        $page = (object)[
            'title'=>'Daftar Mahasiswa Kompensasi yang terdaftar dalam sistem'
        ];

        $activeMenu = 'mhs';
        $user = UserModel::all();
        $periode = PeriodeModel::all();
        return view('mahasiswa.index',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu,'user'=>$user,'periode'=>$periode]);
    }

    public function list(Request $request)
    {
        $jamKompen = jamKompenModel::select('jam_kompen_id', 'akumulasi_jam', 'user_id', 'periode_id')
        -> with('user')
        -> with('periode');

        if ($request->periode_id){
            $jamKompen->where('periode_id',$request->periode_id);
        }
        return DataTables::of($jamKompen)
            ->addIndexColumn()
            ->addColumn('aksi', function ($jamKompen) {
                $btn  = '<button onclick="modalAction(\'' . url('/mahasiswa/' . $jamKompen->jam_kompen_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/mahasiswa/' . $jamKompen->jam_kompen_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/mahasiswa/' . $jamKompen->jam_kompen_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    public function create_ajax()
    {
        // Fetch data for periode and user dropdowns
        $periode = PeriodeModel::select('periode_id', 'periode_nama')->get();
        $user = UserModel::select('user_id', 'nama', 'username')->where('level_id', 3)->get();
        $detailJamKompen = detail_jamKompenModel::all();
        $matkul = MatkulModel::all();

        // Send data to the view for mahasiswa creation form
        return view('mahasiswa.create_ajax')
            ->with('periode', $periode)
            ->with('user', $user)
            ->with('detailJamKompen', $detailJamKompen)
            ->with('matkul', $matkul)
            ;
    }
    public function store_ajax(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'periode_id' => 'required|integer',
            'matkul_id' => 'required|array|min:1',
            'matkul_id.*' => 'required|integer|min:1',
            'jumlah_jam' => 'required|array|min:1',
            'jumlah_jam.*' => 'required|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
    
        DB::beginTransaction();
        try {
            // Cek apakah user_id sudah ada di jam_kompen
            $jamKompen = jamKompenModel::where('user_id', $request->user_id)->first();
    
            if (!$jamKompen) {
                // Buat record baru di jam_kompen
                $akumulasi_jam = array_sum($request->jumlah_jam);
                $jamKompen = jamKompenModel::create([
                    'user_id' => $request->user_id,
                    'periode_id' => $request->periode_id,
                    'akumulasi_jam' => $akumulasi_jam,
                ]);
            }
    
            foreach ($request->matkul_id as $index => $matkul) {
                $detailJamKompen = detail_jamKompenModel::where('jam_kompen_id', $jamKompen->jam_kompen_id)
                    ->where('matkul_id', $matkul)
                    ->first();
    
                if ($detailJamKompen) {
                    // Update jumlah_jam pada matkul yang sama
                    $detailJamKompen->update([
                        'jumlah_jam' => $detailJamKompen->jumlah_jam + $request->jumlah_jam[$index]
                    ]);
                } else {
                    // Tambahkan matkul baru ke detail_jamKompen
                    detail_jamKompenModel::create([
                        'jam_kompen_id' => $jamKompen->jam_kompen_id,
                        'matkul_id' => $matkul,
                        'jumlah_jam' => $request->jumlah_jam[$index],
                    ]);
                }
            }
    
            // Update akumulasi_jam setelah semua operasi selesai
            $jamKompen->update(['akumulasi_jam' => detail_jamKompenModel::where('jam_kompen_id', $jamKompen->jam_kompen_id)->sum('jumlah_jam')]);
    
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data pekerjaan berhasil disimpan'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function edit_ajax($id)
    {
        // Fetch data related to the specific jamKompen
        $jamKompen = jamKompenModel::with('detail_jamKompen', 'user', 'periode')->find($id);
        if (!$jamKompen) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        // Fetch dropdown data
        $periode = PeriodeModel::select('periode_id', 'periode_nama')->get();
        $user = UserModel::select('user_id', 'nama', 'username')->where('level_id', 3)->get();
        $matkul = MatkulModel::all();

        // Pass data to view
        return view('mahasiswa.edit_ajax', compact('jamKompen', 'periode', 'user', 'matkul'));
    }

    public function update_ajax(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'periode_id' => 'required|integer',
            'matkul_id' => 'required|array|min:1',
            'matkul_id.*' => 'required|integer|min:1',
            'jumlah_jam' => 'required|array|min:1',
            'jumlah_jam.*' => 'required|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        DB::beginTransaction();
        try {
            $jamKompen = jamKompenModel::where('user_id', $request->user_id)->first();
    
            if (!$jamKompen) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }
    
            $jamKompen->detail_jamKompen()->delete();
            foreach ($request->matkul_id as $index => $matkul) {
                $detailJamKompen = detail_jamKompenModel::where('jam_kompen_id', $jamKompen->jam_kompen_id)
                    ->where('matkul_id', $matkul)
                    ->first();
    
                if ($detailJamKompen) {
                    // Update jumlah_jam pada matkul yang sama
                    $detailJamKompen->update([
                        'jumlah_jam' => $detailJamKompen->jumlah_jam + $request->jumlah_jam[$index]
                    ]);
                } else {
                    // Tambahkan matkul baru ke detail_jamKompen
                    detail_jamKompenModel::create([
                        'jam_kompen_id' => $jamKompen->jam_kompen_id,
                        'matkul_id' => $matkul,
                        'jumlah_jam' => $request->jumlah_jam[$index],
                    ]);
                }
            }
    
            // Update akumulasi_jam setelah semua operasi selesai
            $jamKompen->update(['akumulasi_jam' => detail_jamKompenModel::where('jam_kompen_id', $jamKompen->jam_kompen_id)->sum('jumlah_jam')]);
    
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function show_ajax(string $id)
    {
        $jamkompen = jamKompenModel::with('detail_jamKompen','user','periode','detail_jamKompen.matkul')->find($id);
        return view('mahasiswa.show_ajax',['jamKompen'=>$jamkompen]);
    }
    public function confirm_ajax(string $jam_kompen_id)
    {
        // Ambil data penjualan berdasarkan ID
        $jamKompen = jamKompenModel::find($jam_kompen_id);

        // Cek apakah data penjualan ditemukan
        if (!$jamKompen) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan.'
            ], 404);
        }

        // Ambil detail penjualan terkait
        $detailJamKompen = detail_jamKompenModel::where('jam_kompen_id', $jam_kompen_id)->get();

        return view('mahasiswa.confirm_ajax', [
            'jamKompen' => $jamKompen,
            'detailJamKompen' => $detailJamKompen
        ]);
    }

    public function delete_ajax(Request $request, string $jam_kompen_id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            // Cari data penjualan
            $jamKompen = jamKompenModel::findOrFail($jam_kompen_id);

            if ($jamKompen) {
                try {
                    // Hapus semua detail penjualan yang terkait
                    detail_jamKompenModel::where('jam_kompen_id', $jam_kompen_id)->delete();

                    // Hapus data penjualan
                    $jamKompen->delete();

                    return response()->json([
                        'status' => true,
                        'message' => 'Data Mahasiswa berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data gagal dihapus karena masih terkait dengan data lain'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Mahasiswa tidak ditemukan'
                ]);
            }
        }

        return redirect('/');
    }

    public function import()
    {
        return view('mahasiswa.import');
    }


    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'file_mahasiswa' => ['required', 'mimes:xlsx', 'max:1024']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('file_mahasiswa');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $data = $spreadsheet->getActiveSheet()->toArray(null, false, true, true);

            if (empty($data) || count($data) <= 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang dapat diimport atau format file salah'
                ]);
            }

            DB::beginTransaction();
            try {
                foreach ($data as $index => $row) {
                    if ($index === 1) continue;

                    if (empty($row['A']) || empty($row['B']) || empty($row['D']) || empty($row['E'])) {
                        throw new \Exception("Data pada baris ke-" . ($index + 1) . " tidak lengkap");
                    }

                    $user = DB::table('m_user')->where('username', $row['A'])->first();
                    if (!$user) {
                        throw new \Exception("Username '{$row['A']}' tidak ditemukan pada data user");
                    }

                    $userId = $user->user_id;
                    $matkulIds = explode(';', $row['D']);
                    $jumlahJams = explode(';', $row['E']);

                    if (count($matkulIds) !== count($jumlahJams)) {
                        throw new \Exception("Jumlah matkul_id dan jumlah jam tidak sesuai pada baris ke-" . ($index + 1));
                    }

                    $jamKompen = jamKompenModel::where('user_id', $userId)->first();

                    if ($jamKompen) {
                        $akumulasi_jam = $jamKompen->akumulasi_jam + array_sum($jumlahJams);
                        $jamKompen->update(['akumulasi_jam' => $akumulasi_jam]);
                    } else {
                        $akumulasi_jam = array_sum($jumlahJams);
                        $jamKompen = jamKompenModel::create([
                            'user_id' => $userId,
                            'periode_id' => $row['B'],
                            'akumulasi_jam' => $akumulasi_jam,
                        ]);
                    }

                    foreach ($matkulIds as $key => $matkulId) {
                        $existingDetail = detail_jamKompenModel::where('jam_kompen_id', $jamKompen->jam_kompen_id)
                            ->where('matkul_id', $matkulId)
                            ->first();

                        if ($existingDetail) {
                            $existingDetail->update([
                                'jumlah_jam' => $existingDetail->jumlah_jam + $jumlahJams[$key]
                            ]);
                        } else {
                            detail_jamKompenModel::create([
                                'jam_kompen_id' => $jamKompen->jam_kompen_id,
                                'matkul_id' => $matkulId,
                                'jumlah_jam' => $jumlahJams[$key],
                                'created_at' => now(),
                            ]);
                        }
                    }
                }

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat mengimport data',
                    'errors' => $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Request tidak valid'
        ], 400);
    }

    public function export_excel()
    {
        // Ambil data jam kompen dan detailnya
        $jamKompen = jamKompenModel::select('jam_kompen_id', 'user_id', 'periode_id', 'akumulasi_jam')
            ->with(['detail_jamKompen.matkul']) // Pastikan relasi sudah terdefinisi
            ->orderBy('periode_id')
            ->get();

        // Load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // Ambil sheet yang aktif

        // Set header untuk penjualan
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Periode');
        $sheet->setCellValue('D1', 'Akumulasi Jam');
        $sheet->setCellValue('E1', 'Mata Kuliah');
        $sheet->setCellValue('F1', 'Jumlah Jam');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true); // Bold header

        $no = 1;  // Nomor data dimulai dari 1
        $baris = 2; // Baris data dimulai dari baris ke 2

        // Loop untuk setiap penjualan
        foreach ($jamKompen as $kompen) {
            // Loop untuk setiap detail penjualan
            foreach ($kompen->detail_jamKompen as $detail) {
                $sheet->setCellValue('A' . $baris, $no);
                $sheet->setCellValue('B' . $baris, $kompen->user->username );
                $sheet->setCellValue('C' . $baris, $kompen->periode->periode_nama ) ;
                $sheet->setCellValue('D' . $baris, $kompen->akumulasi_jam );
                $sheet->setCellValue('E' . $baris, $detail->matkul->matkul_nama );
                $sheet->setCellValue('F' . $baris, $detail->jumlah_jam);

                $baris++;
                $no++;
            }
        }

        // Set auto size untuk kolom
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set title sheet
        $sheet->setTitle('Data Mahasiswa Kompensasi');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Mahasiswa Kompensasi ' . date('Y-m-d H:i:s') . '.xlsx';

        // Pengaturan header untuk download file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;

    }
    public function export_pdf()
    {
        // Ambil data jam kompen dan detailnya

        $jamKompen = jamKompenModel::select('jam_kompen_id', 'user_id', 'periode_id', 'akumulasi_jam')
            ->with(['detail_jamKompen.matkul']) // Pastikan relasi sudah terdefinisi
            ->orderBy('periode_id')
            ->get();


        // Load view untuk PDF
        $pdf = Pdf::loadView('mahasiswa.export_pdf', ['jamKompen' => $jamKompen]);

        $pdf->setPaper('a4', 'portrait'); // Set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // Set true jika ada gambar dari URL
        $pdf->render();

        // Stream file PDF
        return $pdf->stream('Data Mahasiswa Kompensasi' . date('Y-m-d_H-i-s') . '.pdf');
    }

}
