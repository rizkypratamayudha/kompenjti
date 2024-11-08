<?php

namespace App\Http\Controllers;

use App\Models\detail_dosenModel;
use App\Models\detail_kaprodiModel;
use App\Models\detail_mahasiswaModel;
use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
        $user   = UserModel::find($id);

        return view('user.show_ajax', ['user' => $user]);
    }

    public function edit_ajax(string $id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('user.edit_ajax', ['user' => $user, 'level' => $level]);
    }

    public function update_ajax(Request $request, $id)
{
    // cek apakah request dari ajax
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'level_id' => 'required|integer',
            'username' => 'required|max:20|unique:m_user,username,' . $id . ',user_id',
            'nama'     => 'required|max:100',
            'password' => 'nullable|min:6|max:20' // Password masih bisa diisi atau dibiarkan kosong
        ];

        // Validasi request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false, // respon json, true: berhasil, false: gagal
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors() // menunjukkan field mana yang error
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