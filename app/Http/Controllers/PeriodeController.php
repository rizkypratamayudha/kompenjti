<?php

namespace App\Http\Controllers;

use App\Models\PeriodeModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yajra\DataTables\Facades\DataTables;

class PeriodeController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Periode',
            'list' => ['Home', 'Periode']
        ];

        $page = (object)[
            'title' => 'Daftar Periode yang Terdaftar dalam Sistem'
        ];

        $activeMenu = 'periode';

        return view('periode.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
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
                $btn  = '<button onclick="modalAction(\'' . url('/periode/' . $periode->periode_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/periode/' . $periode->periode_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/periode/' . $periode->periode_id . '/confirm_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
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
        // Validasi input
        $rules = [
          'periode_id' => 'required|integer', // Validasi untuk periode_id
          'periode_nama' => 'required|string|max:255' // Validasi untuk nama periode
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors() // Error detail per field
            ]);
        }
        PeriodeModel::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Data periode berhasil disimpan'
        ]);
    }


    public function edit_ajax($periode_id)
    {
        $periode = PeriodeModel::find($periode_id);

        if (!$periode) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return view('periode.edit_ajax', ['periode' => $periode]);
    }

    public function update_ajax(Request $request, $periode_id)
{
    // Memastikan permintaan berasal dari AJAX atau JSON
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'periode_id' => 'required|integer', // Validasi untuk periode_id
            'periode_nama' => 'required|string|max:255' // Validasi untuk nama periode
        ];

        // Validasi input
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false, // Respon JSON, true: berhasil, false: gagal
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors() // Menunjukkan field mana yang error
            ]);
        }

        // Cari data berdasarkan periode_id
        $periode = PeriodeModel::find($periode_id);

        if ($periode) {
            // Update data langsung
            $periode->update($request->all());

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

    // Jika bukan AJAX atau JSON, redirect ke halaman utama
    return redirect('/');
}



    public function confirm_ajax($periode_id)
    {
        $periode = PeriodeModel::find($periode_id);

        return view('periode.confirm_ajax', ['periode' => $periode]);
    }

    public function delete_ajax(Request $request, $periode_id)
    {
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
                    'message' => 'Data gagal dihapus karena terkait dengan data lain'
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Data tidak ditemukan'
        ]);
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

        $row = 2;
        foreach ($periode as $index => $data) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $data->periode_nama);
            $row++;
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Periode_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $periode = PeriodeModel::select('periode_nama')->get();

        $pdf = Pdf::loadView('periode.export_pdf', ['periode' => $periode]);
        return $pdf->stream('Data_Periode_' . date('Ymd_His') . '.pdf');
    }
}
