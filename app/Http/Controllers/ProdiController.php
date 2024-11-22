<?php

namespace App\Http\Controllers;

use App\Models\ProdiModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yajra\DataTables\DataTables as DataTablesDataTables;
use Yajra\DataTables\Facades\DataTables;

class ProdiController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title'=>'Daftar Program Studi',
            'list'=>['Home','Program Studi']
        ];

        $page = (object)[
            'title'=>'Daftar Program Studi yang terdaftar dalam sistem'
        ];

        $activeMenu = 'prodi';
        $prodi = ProdiModel::all();
        return view('prodi.index',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu,'prodi'=>$prodi]);
    }

    public function list(Request $request)
    {
        $prodi = prodiModel::select('prodi_id', 'prodi_nama',);

        if ($request->prodi_id){
            $prodi->where('prodi_id',$request->prodi_id);
        }
        return DataTables::of($prodi)
            ->addIndexColumn()
            ->addColumn('aksi', function ($prodi) { 
                $btn  = '<button onclick="modalAction(\'' . url('/prodi/' . $prodi->prodi_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/prodi/' . $prodi->prodi_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/prodi/' . $prodi->prodi_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) 
            ->make(true);
    }

    public function create_ajax(){
        return view ('prodi.create_ajax');
    }
    public function store_ajax(Request $request){
        if($request->ajax()||$request->wantsJson()){
            $rules = [
                'prodi_nama'=>'required|string|max:100'
            ];

            $validator = Validator::make($request->all(),$rules);
            if($validator->fails()){
                return response()->json([
                    'status'=>false,
                    'message'=>'Validasi Gagal',
                    'msgField'=>$validator->errors()
                ]);
            }

            ProdiModel::create($request->all());
            return response()->json([
                'status'=>true,
                'message'=>'Data Program Studi berhasil disimpan'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(Request $request, $prodi_id){
        $prodi = prodiModel::find($prodi_id);

        return view ('prodi.edit_ajax',['prodi'=>$prodi]);
    }

    public function update_ajax (Request $request, $prodi_id){
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'prodi_nama'=>'required|string|max:100'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,    
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()  
                ]);
            }

            $check = prodimodel::find($prodi_id);
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

    public function confirm_ajax ($prodi_id){
        $prodi = prodiModel::find($prodi_id);

        return view('prodi.confirm_ajax',['prodi'=>$prodi]);
    }

    public function delete_ajax(Request $request, $prodi_id)
{
    if ($request->ajax() || $request->wantsJson()) {
        $prodi = prodiModel::find($prodi_id);

        if ($prodi) {
            try {
                $prodi->delete();
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

    public function show_ajax ($prodi_id){
        $prodi = prodiModel::find($prodi_id);

        return view('prodi.show_ajax',['prodi'=>$prodi]);
    }
    public function import() 
    { 
        return view('prodi.import'); 
    }
    public function import_ajax(Request $request) 
    { 
        if($request->ajax() || $request->wantsJson()){ 
            $rules = [ 

                'file_prodi' => ['required', 'mimes:xlsx', 'max:1024'] 
            ]; 
 
            $validator = Validator::make($request->all(), $rules); 
            if($validator->fails()){ 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Validasi Gagal', 
                    'msgField' => $validator->errors() 
                ]); 
            } 
 
            $file = $request->file('file_prodi');  // ambil file dari request 
 
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
                            'prodi_nama' => $value['A'], 
                            'created_at' => now(), 
                        ]; 
                    } 
                } 
 
                if(count($insert) > 0){ 
                    // insert data ke database, jika data sudah ada, maka diabaikan 
                    prodiModel::insertOrIgnore($insert);    
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
        // Ambil data dari prodimodel
        $prodi = prodimodel::select( 'prodi_nama')->get();

        // Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // Ambil sheet yang aktif

        // Set Header Kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Program Studi');

        // Buat header menjadi bold
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);

        // Isi data
        $no = 1; // Nomor data dimulai dari 1
        $baris = 2; // Baris data dimulai dari baris ke-2
        foreach ($prodi as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->prodi_nama);

            $baris++;
            $no++;
        }

        // Set ukuran kolom otomatis untuk semua kolom
        foreach (range('A', 'B') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul sheet
        $sheet->setTitle('Data prodi');

        // Buat writer untuk menulis file excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data prodi_' . date('Y-m-d_His') . '.xlsx';

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
        $prodi = prodiModel::select( 'prodi_nama') 
        ->get();

        // Gunakan library Dompdf untuk membuat PDF
        $pdf = Pdf::loadView('prodi.export_pdf', ['prodi' => $prodi]);

        // Atur ukuran kertas dan orientasi
        $pdf->setPaper('A4', 'portrait');

        // Aktifkan opsi untuk memuat gambar dari URL (jika ada)
        $pdf->setOption('isRemoteEnabled', true);

        // Render PDF
        $pdf->render();

        // Download PDF
        return $pdf->stream('Data prodi ' . date('Y-m-d H:i:s') . '.pdf');
    }
}