<?php

namespace App\Http\Controllers;

use App\Models\kompetensi_adminModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class kompetensi_adminController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Page Tambah Kompetensi',
            'list' => ['Home', 'Kompetensi'],
        ];

        $page = (object)[
            'title' => 'Page Tambah Kompetensi',
        ];

        $activeMenu = 'kompetensi_admin';
        return view('kompetensi_admin.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $kompetensi = kompetensi_adminModel::select('kompetensi_admin_id', 'kompetensi_nama',);

        if ($request->kompetensi_admin_id) {
            $kompetensi->where('kompetensi_admin_id', $request->kompetensi_id);
        }
        return DataTables::of($kompetensi)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kompetensi) { // menambahkan kolom aksi
                $btn  = '<button onclick="modalAction(\'' . url('/kompetensi_admin/' . $kompetensi->kompetensi_admin_id .
                    '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kompetensi_admin/' . $kompetensi->kompetensi_admin_id .
                    '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kompetensi_admin/' . $kompetensi->kompetensi_admin_id .
                    '/confirm_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create_ajax()
    {
        return view('kompetensi_admin.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kompetensi_nama' => 'required|string|max:100'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            kompetensi_adminModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data level berhasil disimpan'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax($id)
    {
        $kompetensi = kompetensi_adminModel::find($id);
        return view('kompetensi_admin.edit_ajax', ['kompetensi' => $kompetensi]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kompetensi_nama' => 'required|string|max:100'
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

            $check = kompetensi_adminModel::find($id);
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

    public function confirm_ajax($id)
    {
        $kompetensi = kompetensi_adminModel::find($id);
        return view('kompetensi_admin.confirm_ajax', ['kompetensi' => $kompetensi]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $kompetensi = kompetensi_adminModel::find($id);

            if ($kompetensi) {
                try {
                    $kompetensi->delete();
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

    public function show_ajax($id){
        $kompetensi = kompetensi_adminModel::find($id);
        return view('kompetensi_admin.show_ajax',['kompetensi'=>$kompetensi]);
    }
}
