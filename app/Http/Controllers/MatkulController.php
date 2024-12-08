<?php

namespace App\Http\Controllers;

use App\Models\matkulModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yajra\DataTables\DataTables as DataTablesDataTables;
use Yajra\DataTables\Facades\DataTables;

class MatkulController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title'=>'Daftar Mata Kuliah',
            'list'=>['Home','Mata Kuliah']
        ];

        $page = (object)[
            'title'=>'Daftar Mata Kuliah yang terdaftar dalam sistem'
        ];

        $activeMenu = 'matkul';
        $matkul = matkulModel::all();
        return view('matkul.index',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu,'matkul'=>$matkul]);
    }

    public function list(Request $request)
    {
        $matkul = matkulModel::select('matkul_id', 'matkul_kode', 'matkul_nama',);

        if ($request->matkul_id){
            $matkul->where('matkul_id',$request->matkul_id);
        }
        return DataTables::of($matkul)
            ->addIndexColumn()
            ->addColumn('aksi', function ($matkul) { 
                $btn  = '<button onclick="modalAction(\'' . url('/matkul/' . $matkul->matkul_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/matkul/' . $matkul->matkul_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/matkul/' . $matkul->matkul_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) 
            ->make(true);
    }

    public function create_ajax(){
        return view ('matkul.create_ajax');
    }
    public function store_ajax(Request $request){
        if($request->ajax()||$request->wantsJson()){
            $rules = [
                'matkul_kode'=>'required|string|max:100',
                'matkul_nama'=>'required|string|max:100'
            ];

            $validator = Validator::make($request->all(),$rules);
            if($validator->fails()){
                return response()->json([
                    'status'=>false,
                    'message'=>'Validasi Gagal',
                    'msgField'=>$validator->errors()
                ]);
            }

            matkulmodel::create($request->all());
            return response()->json([
                'status'=>true,
                'message'=>'Data Mata Kuliah berhasil disimpan'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(Request $request, $matkul_id){
        $matkul = matkulModel::find($matkul_id);

        return view ('matkul.edit_ajax',['matkul'=>$matkul]);
    }

    public function update_ajax (Request $request, $matkul_id){
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'matkul_kode'=>'required|string|max:100',
                'matkul_nama'=>'required|string|max:100'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,    
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()  
                ]);
            }

            $check = matkulmodel::find($matkul_id);
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

    public function confirm_ajax ($matkul_id){
        $matkul = matkulModel::find($matkul_id);

        return view('matkul.confirm_ajax',['matkul'=>$matkul]);
    }

    public function delete_ajax(Request $request, $matkul_id)
{
    if ($request->ajax() || $request->wantsJson()) {
        $matkul = matkulModel::find($matkul_id);

        if ($matkul) {
            try {
                $matkul->delete();
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

    public function show_ajax ($matkul_id){
        $matkul = matkulModel::find($matkul_id);

        return view('matkul.show_ajax',['matkul'=>$matkul]);
    }
    public function import() 
    { 
        return view('matkul.import'); 
    }
    public function import_ajax(Request $request) 
    { 
        if($request->ajax() || $request->wantsJson()){ 
            $rules = [ 

                'file_matkul' => ['required', 'mimes:xlsx', 'max:1024'] 
            ]; 
 
            $validator = Validator::make($request->all(), $rules); 
            if($validator->fails()){ 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Validasi Gagal', 
                    'msgField' => $validator->errors() 
                ]); 
            } 
 
            $file = $request->file('file_matkul');  // ambil file dari request 
 
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
                            'matkul_kode'=> $value['A'],
                            'matkul_nama' => $value['B'], 
                            'created_at' => now(), 
                        ]; 
                    } 
                } 
 
                if(count($insert) > 0){ 
                    // insert data ke database, jika data sudah ada, maka diabaikan 
                    matkulModel::insertOrIgnore($insert);    
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
        // Ambil data dari matkulmodel
        $matkul = matkulmodel::select( 'matkul_kode', 'matkul_nama')->get();

        // Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // Ambil sheet yang aktif

        // Set Header Kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Mata Kuliah');
        $sheet->setCellValue('C1', 'Nama Mata Kuliah');

        // Buat header menjadi bold
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        // Isi data
        $no = 1; // Nomor data dimulai dari 1
        $baris = 2; // Baris data dimulai dari baris ke-2
        foreach ($matkul as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->matkul_kode);
            $sheet->setCellValue('C' . $baris, $value->matkul_nama);

            $baris++;
            $no++;
        }

        // Set ukuran kolom otomatis untuk semua kolom
        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul sheet
        $sheet->setTitle('Data matkul');

        // Buat writer untuk menulis file excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data matkul_' . date('Y-m-d_His') . '.xlsx';

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
        $matkul = matkulModel::select( 'matkul_kode', 'matkul_nama') 
        ->get();

        // Gunakan library Dompdf untuk membuat PDF
        $pdf = Pdf::loadView('matkul.export_pdf', ['matkul' => $matkul]);

        // Atur ukuran kertas dan orientasi
        $pdf->setPaper('A4', 'portrait');

        // Aktifkan opsi untuk memuat gambar dari URL (jika ada)
        $pdf->setOption('isRemoteEnabled', true);

        // Render PDF
        $pdf->render();

        // Download PDF
        return $pdf->stream('Data matkul ' . date('Y-m-d H:i:s') . '.pdf');
    }
}