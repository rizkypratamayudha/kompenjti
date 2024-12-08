<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yajra\DataTables\DataTables as DataTablesDataTables;
use Yajra\DataTables\Facades\DataTables;

class LevelController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title'=>'Daftar Roles',
            'list'=>['Home','Roles']
        ];

        $page = (object)[
            'title'=>'Daftar Role yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level';
        $level = LevelModel::all();
        return view('level.index',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu,'level'=>$level]);
    }

    public function list(Request $request)
    {
        $level = LevelModel::select('level_id', 'kode_level', 'level_nama',);

        if ($request->level_id){
            $level->where('level_id',$request->level_id);
        }
        return DataTables::of($level)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($level) {
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create_ajax(){
        return view ('level.create_ajax');
    }
    public function store_ajax(Request $request){
        if($request->ajax()||$request->wantsJson()){
            $rules = [
                'kode_level'=>'required|string|unique:m_level,kode_level',
                'level_nama'=>'required|string|max:100'
            ];

            $validator = Validator::make($request->all(),$rules);
            if($validator->fails()){
                return response()->json([
                    'status'=>false,
                    'message'=>'Validasi Gagal',
                    'msgField'=>$validator->errors()
                ]);
            }

            levelmodel::create($request->all());
            return response()->json([
                'status'=>true,
                'message'=>'Data level berhasil disimpan'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(Request $request, $level_id){
        $level = LevelModel::find($level_id);

        return view ('level.edit_ajax',['level'=>$level]);
    }

    public function update_ajax (Request $request, $level_id){
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kode_level' => 'required|string|unique:m_level,kode_level,' .$level_id.',level_id',
                'level_nama'=>'required|string|max:100'

            ];
            // use Illuminate\Support\Facades\Validator;
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,    // respon json, true: berhasil, false: gagal
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()  // menunjukkan field mana yang error
                ]);
            }

            $check = levelmodel::find($level_id);
            if ($check) {
                $check->update($request->all());
                return response()->json([
                    'status'  => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax ($level_id){
        $level = LevelModel::find($level_id);

        return view('level.confirm_ajax',['level'=>$level]);
    }

    public function delete_ajax(Request $request, $level_id)
{
    if ($request->ajax() || $request->wantsJson()) {
        $level = LevelModel::find($level_id);

        if ($level) {
            try {
                $level->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
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

    return redirect('/');
}

    public function show_ajax ($level_id){
        $level = LevelModel::find($level_id);

        return view('level.show_ajax',['level'=>$level]);
    }
    public function import() 
    { 
        return view('level.import'); 
    }
    public function import_ajax(Request $request) 
    { 
        if($request->ajax() || $request->wantsJson()){ 
            $rules = [ 
                // validasi file harus xls atau xlsx, max 1MB 
                'file_level' => ['required', 'mimes:xlsx', 'max:1024'] 
            ]; 
 
            $validator = Validator::make($request->all(), $rules); 
            if($validator->fails()){ 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Validasi Gagal', 
                    'msgField' => $validator->errors() 
                ]); 
            } 
 
            $file = $request->file('file_level');  // ambil file dari request 
 
            $reader = IOFactory::createReader('Xlsx');  // load reader file excel 
            $reader->setReadDataOnly(true);             // hanya membaca data 
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel 
            $sheet = $spreadsheet->getActiveSheet();    // ambil sheet yang aktif 
 
            $data = $sheet->toArray(null, false, true, true);   // ambil data excel 
 
            $insert = []; 
            if(count($data) > 1){ // jika data lebih dari 1 baris 
                foreach ($data as $baris => $value) { 
                    if($baris > 1){ // baris ke 1 adalah header, maka lewati 
                        $insert[] = [ 
                            'kode_level' => $value['A'], 
                            'level_nama' => $value['B'],
                            'created_at' => now(), 
                        ]; 
                    } 
                } 
 
                if(count($insert) > 0){ 
                    // insert data ke database, jika data sudah ada, maka diabaikan 
                    LevelModel::insertOrIgnore($insert);    
                } 
 
                return response()->json([ 
                    'status' => true, 
                    'message' => 'Data berhasil diimport' 
                ]); 
            }else{ 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Tidak ada data yang diimport' 
                ]); 
            } 
        } 
        return redirect('/'); 
    }
        public function export_excel()
    {
        // Ambil data dari Levelmodel
        $barang = Levelmodel::select('kode_level', 'level_nama')->get();

        // Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // Ambil sheet yang aktif

        // Set Header Kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'kode Level');
        $sheet->setCellValue('C1', 'Nama Level');

        // Buat header menjadi bold
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        // Isi data
        $no = 1; // Nomor data dimulai dari 1
        $baris = 2; // Baris data dimulai dari baris ke-2
        foreach ($barang as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->kode_level);
            $sheet->setCellValue('C' . $baris, $value->level_nama);

            $baris++;
            $no++;
        }

        // Set ukuran kolom otomatis untuk semua kolom
        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul sheet
        $sheet->setTitle('Data Level');

        // Buat writer untuk menulis file excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Level_' . date('Y-m-d_His') . '.xlsx';

        // Atur Header untuk Download File Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        // Simpan file dan kirim ke output
        $writer->save('php://output');
        exit;
    }
    public function export_pdf()
    {
        // Ambil data barang dari database
        $level = LevelModel::select('kode_level', 'level_nama') 
        ->get();

        // Gunakan library Dompdf untuk membuat PDF
        $pdf = Pdf::loadView('level.export_pdf', ['level' => $level]);

        // Atur ukuran kertas dan orientasi
        $pdf->setPaper('A4', 'portrait');

        // Aktifkan opsi untuk memuat gambar dari URL (jika ada)
        $pdf->setOption('isRemoteEnabled', true);

        // Render PDF
        $pdf->render();

        // Download PDF
        return $pdf->stream('Data Level ' . date('Y-m-d H:i:s') . '.pdf');
    }
}