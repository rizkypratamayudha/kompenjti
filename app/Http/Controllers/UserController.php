<?php

namespace App\Http\Controllers;

use App\Models\detail_dosenModel;
use App\Models\detail_kaprodiModel;
use App\Models\detail_mahasiswaModel;
use App\Models\LevelModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar user',
            'list' => ['Home', 'user'],
        ];

        $page = (object)[
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'user'; //set menu yang aktif
        $level = LevelModel::all(); //mengambil data level untuk filter level
        return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'level' => $level]);
    }

    public function list(Request $request)
    {
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id')
            ->with('level');

        // Filter data user berdasarkan level_id
        if ($request->level_id) {
            $users->where('level_id', $request->level_id);
        }

        return DataTables::of($users)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($user) { // menambahkan kolom aksi
                /*$btn = '<a href="' . url('/user/' . $user->user_id) . '" class="btn btn-info btn-sm">Detail</a> ';
            $btn .= '<a href="' . url('/user/' . $user->user_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
            $btn .= '<form class="d-inline-block" method="POST" action="' . url('/user/' . $user->user_id) . '">'
                . csrf_field() . method_field('DELETE') .
                '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';*/
                $btn  = '<button onclick="modalAction(\'' . url('/user/' . $user->user_id .
                    '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/user/' . $user->user_id .
                    '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/user/' . $user->user_id .
                    '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create_ajax (){
        $level = LevelModel::select('level_id','level_nama')->get();
        return view('user.create_ajax',['level'=>$level]);
    }

    public function store_ajax(Request $request)
{
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'level_id' => 'required|integer',
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama' => 'required|string|max:100',
            'email' => 'required|email',
            'no_hp' => 'required|string',
            'password' => 'required|min:6'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors(),
            ]);
        }

        // Hash the password before saving the user
        $data = $request->all();
        $data['password'] = Hash::make($request->password); // Hash the password

        UserModel::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Data user berhasil disimpan'
        ]);
    }
    return redirect('/');
}

public function show_ajax(string $id)
    {
        $user = UserModel::with(['detailMahasiswa', 'detailDosen', 'detailKaprodi'])->find($id);

    // Tentukan sumber data no_hp dan email berdasarkan peran
    if ($user->role == 'mahasiswa' && $user->detailMahasiswa) {
        $contact = $user->detailMahasiswa;
    } elseif ($user->role == 'dosen' && $user->detailDosen) {
        $contact = $user->detailDosen;
    } elseif ($user->role == 'kaprodi' && $user->detailKaprodi) {
        $contact = $user->detailKaprodi;
    } else {
        $contact = null;
    }

    return view('user.show_ajax', [
        'user' => $user,
        'contact' => $contact,
    ]);
    }

    public function edit_ajax(string $id)
    {
        $user = UserModel::with(['detailMahasiswa', 'detailDosen', 'detailKaprodi'])->find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();

    if ($user->role == 'mahasiswa' && $user->detailMahasiswa) {
        $contact = $user->detailMahasiswa;
    } elseif ($user->role == 'dosen' && $user->detailDosen) {
        $contact = $user->detailDosen;
    } elseif ($user->role == 'kaprodi' && $user->detailKaprodi) {
        $contact = $user->detailKaprodi;
    } else {
        $contact = null;
    }

    return view('user.edit_ajax', [
        'user' => $user,
        'level' => $level,
        'contact' => $contact,
    ]);
    }

    public function update_ajax(Request $request, $id)
{
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'level_id' => 'required|integer',
            'username' => 'required|max:20|unique:m_user,username,' . $id . ',user_id',
            'nama'     => 'required|max:100',
            'email' => 'required|email',
            'no_hp' => 'required|string',
            'password' => 'nullable|min:6|max:20'
        ];

        // Validasi request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ]);
        }

        $user = UserModel::find($id);
        if ($user) {
            // Jika password diisi, hash password sebelum menyimpan
            if ($request->filled('password')) {
                $request->merge(['password' => Hash::make($request->password)]);
            } else {
                // Jika tidak diisi, hapus password dari request
                $request->request->remove('password');
            }

            // Update data user
            $user->update($request->all());

            // Update no_hp dan email sesuai dengan peran menggunakan hasRole
            if ($user->hasRole('mahasiswa') && $user->detailMahasiswa) {
                $user->detailMahasiswa->update([
                    'no_hp' => $request->no_hp,
                    'email' => $request->email,
                ]);
            } elseif ($user->hasRole('dosen') && $user->detailDosen) {
                $user->detailDosen->update([
                    'no_hp' => $request->no_hp,
                    'email' => $request->email,
                ]);
            } elseif ($user->hasRole('kaprodi') && $user->detailKaprodi) {
                $user->detailKaprodi->update([
                    'no_hp' => $request->no_hp,
                    'email' => $request->email,
                ]);
            }

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


public function confirm_ajax(string $id)
    {
        $user   = UserModel::find($id);

        return view('user.confirm_ajax', ['user' => $user]);
    }

    public function delete_ajax(Request $request, $id)
{
    if ($request->ajax() || $request->wantsJson()) {
        $user = UserModel::find($id);

        if ($user) {
            DB::beginTransaction(); // Mulai transaksi untuk memastikan integritas data
            try {
                // Hapus data terkait berdasarkan level_id
                if ($user->level_id == 2) {
                    detail_dosenModel::where('user_id', $id)->delete();
                } elseif ($user->level_id == 3) {
                    detail_mahasiswaModel::where('user_id', $id)->delete();
                } elseif ($user->level_id == 4) {
                    detail_kaprodiModel::where('user_id', $id)->delete();
                }

                // Hapus data utama dari UserModel
                $user->delete();

                DB::commit(); // Commit transaksi jika semua operasi berhasil
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus beserta data terkait'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); // Batalkan transaksi jika terjadi kesalahan
                return response()->json([
                    'status' => false,
                    'message' => 'Data gagal dihapus karena terjadi kesalahan: ' . $e->getMessage()
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
 public function import() 
    { 
        return view('user.import'); 
    }
    public function import_ajax(Request $request) 
    { 
        if($request->ajax() || $request->wantsJson()){ 
            $rules = [ 
                // validasi file harus xls atau xlsx, max 1MB 
                'file_user' => ['required', 'mimes:xlsx', 'max:1024'] 
            ]; 
 
            $validator = Validator::make($request->all(), $rules); 
            if($validator->fails()){ 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Validasi Gagal', 
                    'msgField' => $validator->errors() 
                ]); 
            } 
 
            $file = $request->file('file_user');  // ambil file dari request 
 
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
                            'level_id' => $value['A'], 
                            'username' => $value['B'], 
                            'nama' => $value['C'], 
                            'password' =>Hash::make($value['D']),  
                            'created_at' => now(), 
                        ]; 
                    } 
                } 
 
                if(count($insert) > 0){ 
                    // insert data ke database, jika data sudah ada, maka diabaikan 
                    UserModel::insertOrIgnore($insert);    
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
        $user = usermodel::select('level_id', 'username', 'nama')
            ->orderBy('level_id')
            ->with('level')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); //ambil sheet yang aktif

        // Set Header Kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Level ID');
        $sheet->setCellValue('C1', 'Username');
        $sheet->setCellValue('D1', 'Nama');
        $sheet->setCellValue('E1', 'Nama Level');;

        // Buat header menjadi bold
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $no = 1; // Nomor data dimulai dari 1
        $baris = 2; // Baris data dimulai dari baris ke-2
        foreach ($user as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->level_id);
            $sheet->setCellValue('C' . $baris, $value->username);
            $sheet->setCellValue('D' . $baris, $value->nama);
            $sheet->setCellValue('E' . $baris, $value->level->level_nama);

            $baris++;
            $no++;
        }

        // Set ukuran kolom otomatis untuk semua kolom
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul sheet
        $sheet->setTitle('Data Barang');

        // Buat writer
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data User  ' . date('Y-m-d H:i:s') . '.xlsx';

        // Atur Header untuk Download File Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        // Simpan file dan kirim ke output
        $writer->save('php://output');
        exit;
    }
    public function export_pdf()
{
    // Ambil data user dari database
    $user = usermodel::select('level_id', 'username', 'nama') // Pilih kolom yang diperlukan
        ->orderBy('level_id')
        ->orderBy('username')
        ->with('level') 
        ->get();

    // Gunakan library Dompdf untuk membuat PDF
    $pdf = Pdf::loadView('user.export_pdf', ['user' => $user]); // Ganti 'User' menjadi 'user'

    // Atur ukuran kertas dan orientasi
    $pdf->setPaper('A4', 'portrait');

    // Aktifkan opsi untuk memuat gambar dari URL (jika ada)
    $pdf->setOption('isRemoteEnabled', true);
    
    // Render PDF dan tampilkan di browser
    return $pdf->stream('Data User ' . date('Y-m-d H:i:s') . '.pdf');
}   

public function profile()
{
    $breadcrumb = (object)[
        'title' => 'Profil Saya',
        'list' => ['Home', 'Profil'],
    ];

    $page = (object)[
        'title' => 'Edit Profil Pengguna'
    ];
    
    $activeMenu = 'profile'; // Set menu yang aktif

    // Ambil data pengguna yang sedang login
    $user = Auth::user();

    // Pastikan user tidak null
    if (!$user) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    // Ambil level_id dan level_nama dari tabel m_level
    $level_id = $user->level ? $user->level->level_id : null;
    $level_nama = $user->level ? $user->level->level_nama : 'Tidak ada level'; 

    return view('profile.index', [
        'breadcrumb' => $breadcrumb,
        'page' => $page,
        'activeMenu' => $activeMenu,
        'user' => $user,
        'level_id' => $level_id,
        'level_nama' => $level_nama // Kirim level_nama dan level_id ke view
    ]);
}
    public function update_profile(Request $request){
        $avatar = $request->file('avatar')->store('avatars');
        $request->user()->update([
           'avatar' => $avatar
        ]);

        return redirect()->back();
         
}

public function updateinfo(Request $request){
    if($request->ajax() || $request->wantsJson()){
        $rules = [
        'level_id' => 'required|integer',
        'username' => 'nullable|max:20|unique:m_user,username',
        'nama'     => 'nullable|max:100',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false, // respon json, true: berhasil, false: gagal
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors() // menunjukkan field mana yang error
            ]);
        }

        $user = $request->user();

        if ($user){

            $user->update($request->all());
            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil diupdate'
            ]);
        }
        else {
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }
    return redirect('/');
}

public function update_password(Request $request)
{
    // Validasi input
    $rules = [
        'current_password' => 'required', // Menambahkan validasi password lama
        'password' => 'required|min:5|confirmed', // Menggunakan field password_confirmation secara otomatis
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validasi gagal.',
            'msgField' => $validator->errors() // Mengembalikan pesan error untuk masing-masing field
        ]);
    }

    // Ambil user yang sedang login
    $user = $request->user();

    
    if ($user) {
        // Cek apakah password lama cocok
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password lama tidak sesuai',
            ]);
        }

        // Update password baru dengan hash
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diupdate'
        ]);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'User tidak ditemukan'
        ]);
    }
}

public function deleteAvatar(Request $request)
{
    $user = $request->user(); // Get the authenticated user

    if ($user) {
        // If there's an existing avatar, delete it from storage
        if ($user->avatar) {
            $avatarPath = public_path('storage/' . $user->avatar);
            if (file_exists($avatarPath)) {
                unlink($avatarPath); // Delete the file
            }

            // Set the avatar attribute to null and save the user
            $user->avatar = null;
            $user->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Foto profil berhasil dihapus.'
        ]);
    }

    return response()->json([
        'status' => false,
        'message' => 'User tidak ditemukan.'
    ]);
}

}