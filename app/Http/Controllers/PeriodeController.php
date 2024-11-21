<?php

namespace App\Http\Controllers;

use App\Models\PeriodeModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yajra\DataTables\Facades\DataTables;

class PeriodeController extends Controller
{
     public function index(){
          $breadcrumb = (object)[
              'title'=>'Daftar Periode',
              'list'=>['Home','Periode']
          ];
  
          $page = (object)[
              'title'=>'Daftar Periode yang terdaftar dalam sistem'
          ];
  
          $activeMenu = 'periode';
          $periode = PeriodeModel::all();
          return view('periode.index',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu,'periode'=>$periode]);
      }

    public function list(Request $request)
    {
        $periode = PeriodeModel::select('periode_id', 'periode_nama');

        if ($request->periode_id) {
            $periode->where('periode_id', $request->periode_id);
        }

        return DataTables::of($periode)
        ->addIndexColumn()
        ->addColumn('aksi', function ($periode) {
            $btn  = '<button onclick="modalAction(\'' . url('/periode/' . $periode->periode_id .
                '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
            $btn .= '<button onclick="modalAction(\'' . url('/periode/' . $periode->periode_id .
                '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
            $btn .= '<button onclick="modalAction(\'' . url('/periode/' . $periode->periode_id .
                '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
            return $btn;
        })
        ->rawColumns(['aksi'])
        ->make(true);
}

    public function create_ajax()
    {
        return view('periode.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'periode_nama' => 'required|string|max:100'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            PeriodeModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data periode berhasil disimpan'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(Request $request, $periode_id)
    {
        $periode = PeriodeModel::find($periode_id);

        return view('periode.edit_ajax', ['periode' => $periode]);
    }

    public function update_ajax(Request $request, $periode_id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'periode_nama' => 'required|string|max:100'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $check = PeriodeModel::find($periode_id);
            if ($check) {
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax($periode_id)
    {
        $periode = PeriodeModel::find($periode_id);

        return view('periode.confirm_ajax', ['periode' => $periode]);
    }

    public function delete_ajax(Request $request, $periode_id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $periode = PeriodeModel::find($periode_id);

            if ($periode) {
                try {
                    $periode->delete();
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

    public function show_ajax($periode_id)
    {
        $periode = PeriodeModel::find($periode_id);

        return view('periode.show_ajax', ['periode' => $periode]);
    }

    public function export_excel()
    {
        $periode = PeriodeModel::select('periode_nama')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Periode');

        $sheet->getStyle('A1:B1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($periode as $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->periode_nama);

            $baris++;
            $no++;
        }

        foreach (range('A', 'B') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Periode');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Periode_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $periode = PeriodeModel::select('periode_nama')->get();

        $pdf = Pdf::loadView('periode.export_pdf', ['periode' => $periode]);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->stream('Data_Periode_' . date('Y-m-d_His') . '.pdf');
    }
}