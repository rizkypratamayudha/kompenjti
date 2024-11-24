<?php

namespace App\Http\Controllers;

use App\Models\detail_jamKompenModel;
use App\Models\jamKompenModel;
use App\Models\MatkulModel;
use App\Models\PeriodeModel;
use App\Models\UserModel;
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
            'list'=>['Home','Jam Kompen']
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
            'akumulasi_jam' => 'required|integer',
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

        $akumulasi_jam = array_sum($request->jumlah_jam);


        DB::beginTransaction();
        try {
            $jam_kompen = jamKompenModel::create([
                'user_id' => $request->user_id,
                'periode_id' => $request->periode_id,
                'akumulasi_jam' => $akumulasi_jam,
            ]);

            foreach ($request->matkul_id as $index => $matkul) {
                detail_jamKompenModel::create([
                    'jam_kompen_id' => $jam_kompen->jam_kompen_id,
                    'matkul_id' => $matkul,
                    'jumlah_jam' => $request->jumlah_jam[$index],
                ]);
            }

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
        $jamKompen = jamKompenModel::with('detail_jamKompen', 'user', 'periode')->find($id);
        $periode = PeriodeModel::select('periode_id', 'periode_nama')->get();
        $user = UserModel::select('user_id', 'nama', 'username')->where('level_id', 3)->get();
        $matkul = MatkulModel::all();


        return view('mahasiswa.edit_ajax', compact('jamKompen', 'periode', 'user', 'matkul'));
    }

public function update_ajax(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|integer',
        'periode_id' => 'required|integer',
        'matkul_id' => 'required|array|min:1',
        'matkul_id.*' => 'required|integer',
        'jumlah_jam' => 'required|array|min:1',
        'jumlah_jam.*' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors(),
        ], 422);
    }

    $jamKompen = jamKompenModel::find($id);
    if (!$jamKompen) {
        return response()->json([
            'status' => false,
            'message' => 'Data tidak ditemukan',
        ], 404);
    }

    DB::beginTransaction();
    try {
        $jamKompen->update([
            'user_id' => $request->user_id,
            'periode_id' => $request->periode_id,
        ]);

        $jamKompen->details()->delete();
        foreach ($request->matkul_id as $index => $matkul) {
            detail_jamKompenModel::create([
                'jam_kompen_id' => $jamKompen->jam_kompen_id,
                'matkul_id' => $matkul,
                'jumlah_jam' => $request->jumlah_jam[$index],
            ]);
        }

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

        $jamKompen = jamKompenModel::with(['user', 'periode', 'detail_jamKompen.matkul'])->find($id);


        if ($jamKompen) {

            return view('mahasiswa.show_ajax', [
                'jamKompen' => $jamKompen
            ]);
        } else {

            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
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
    

}
