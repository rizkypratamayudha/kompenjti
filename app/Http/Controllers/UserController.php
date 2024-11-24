<?php

namespace App\Http\Controllers;

use App\Models\detail_dosenModel;
use App\Models\detail_kaprodiModel;
use App\Models\detail_mahasiswaModel;
use App\Models\LevelModel;
use App\Models\PeriodeModel;
use App\Models\ProdiModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

    public function create_ajax()
    {
        // Mengambil semua level, prodi, dan periode untuk ditampilkan pada form
        $level = LevelModel::all();
        $prodi = ProdiModel::all();
        $user = UserModel::all();
        $periode = PeriodeModel::all();

        return view('user.create_ajax', ['level' => $level, 'user' => $user, 'prodi' => $prodi, 'periode' => $periode]);
    }

    public function store_ajax(Request $request)
{
    // Menentukan aturan validasi
    $rules = [
        'level_id' => 'required|integer',
        'username' => 'required|string|min:3|unique:m_user,username',
        'nama' => 'required|string|max:100',
        'password' => 'required|min:6',
        'email' => 'nullable|email',  // Email tidak wajib untuk admin
        'no_hp' => 'nullable|string', // No hp tidak wajib untuk admin
        'prodi_id' => 'nullable|integer', // Prodi id tidak wajib untuk admin
        'angkatan' => 'nullable|integer', // Angkatan tidak wajib untuk admin
        'periode_id' => 'nullable|integer', // Periode id tidak wajib untuk admin
    ];

    // Menambahkan aturan validasi untuk level selain 1 (admin)
    if ($request->level_id != 1) {
        // Aturan validasi untuk dosen, mahasiswa, dan kaprodi
        $rules['email'] = 'required_if:level_id,2,3,4|email';
        $rules['no_hp'] = 'required_if:level_id,2,3,4|string';

        // Aturan validasi untuk prodi_id, angkatan, dan periode_id hanya berlaku untuk mahasiswa dan kaprodi
        if ($request->level_id == 3) {
            $rules['prodi_id'] = 'required|integer';
            $rules['angkatan'] = 'required|integer';
            $rules['periode_id'] = 'required|integer';
        } else if ($request->level_id == 4) {
            $rules['prodi_id'] = 'required|integer';
        }
    }

    // Melakukan validasi
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validasi Gagal',
            'msgField' => $validator->errors(),
        ], 422);
    }

    // Enkripsi password
    $password = Hash::make($request->password);

    DB::beginTransaction();
    try {
        // Menyimpan data user
        $user = UserModel::create([
            'level_id' => $request->level_id,
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => $password,
        ]);

        // Menyimpan data sesuai dengan level_id
        if ($request->level_id == 2) {
            // Dosen
            detail_dosenModel::create([
                'user_id' => $user->user_id,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
            ]);
        } elseif ($request->level_id == 3) {
            // Mahasiswa
            detail_mahasiswaModel::create([
                'user_id' => $user->user_id,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'angkatan' => $request->angkatan,
                'prodi_id' => $request->prodi_id,
                'periode_id' => $request->periode_id,
            ]);
        } elseif ($request->level_id == 4) {
            // Kaprodi
            detail_kaprodiModel::create([
                'user_id' => $user->user_id,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'prodi_id' => $request->prodi_id,
            ]);
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Data User berhasil disimpan'
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
        $prodi = ProdiModel::all(); // Ambil semua data prodi

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        $contact = null;
        // Menentukan detail berdasarkan level pengguna
        if ($user->level_id == 3) { // Mahasiswa
            $contact = $user->detailMahasiswa;
        } elseif ($user->level_id == 2) { // Dosen
            $contact = $user->detailDosen;
        } elseif ($user->level_id == 4) { // Kaprodi
            $contact = $user->detailKaprodi;
        }

        // Kirim data prodi ke view
        return view('user.edit_ajax', [
            'user' => $user,
            'level' => $level,
            'contact' => $contact,
            'prodi' => $prodi, // Pastikan data prodi dikirim ke tampilan
        ]);
    }

    public function update_ajax(Request $request, $user_id)
    {
        // Validation of inputs
        $validated = $request->validate([
            'username' => 'required|string|min:3|max:20',
            'nama' => 'required|string|min:3|max:100',
            'password' => 'nullable|string|min:6|max:20',
            'email' => 'nullable|string|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'angkatan' => 'nullable|string|max:50', // For mahasiswa (student)
            'prodi_id' => 'nullable|integer', // For mahasiswa (student) and kaprodi (head of program)
        ]);

        $user = UserModel::findOrFail($user_id); // Fetch user by user_id

        // Check if admin is updating the user, they can't change the level_id
        if (auth()->user()->level_id == 1) { // Only admin (level_id 1) can edit
            $user->username = $request->username;
            $user->nama = $request->nama;

            if ($request->password) {
                $user->password = bcrypt($request->password); // If password provided, hash it
            }

            $user->save(); // Save user details

            // Check if the user is a mahasiswa (student), dosen (lecturer), or kaprodi (head of program), and update the respective details
            if ($user->level_id == 2) { // Dosen (Lecturer)
                $detail = detail_dosenModel::where('user_id', $user_id)->first();
                if ($detail) {
                    $detail->email = $request->email;
                    $detail->no_hp = $request->no_hp;
                    $detail->save(); // Update lecturer details
                }
            } elseif ($user->level_id == 3) { // Mahasiswa (Student)
                $detail = detail_mahasiswaModel::where('user_id', $user_id)->first();
                if ($detail) {
                    $detail->email = $request->email;
                    $detail->no_hp = $request->no_hp;
                    $detail->angkatan = $request->angkatan; // Update angkatan (year of entry) for mahasiswa
                    $detail->prodi_id = $request->prodi_id; // Update prodi_id (study program) for mahasiswa
                    $detail->save(); // Update student details
                }
            } elseif ($user->level_id == 4) { // Kaprodi (Head of Program)
                $detail = detail_kaprodiModel::where('user_id', $user_id)->first();
                if ($detail) {
                    $detail->email = $request->email;
                    $detail->no_hp = $request->no_hp;
                    $detail->prodi_id = $request->prodi_id; // Update prodi_id for kaprodi
                    $detail->save(); // Update head of program details
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'User data updated successfully!'
            ]);
        } else {
            // If the user is not an admin, return a failure response
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to edit user data!'
            ]);
        }
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
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // validasi file harus xls atau xlsx, max 1MB
                'file_user' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
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
            if (count($data) > 1) { // jika data lebih dari 1 baris
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // baris ke 1 adalah header, maka lewati
                        $insert[] = [
                            'level_id' => $value['A'],
                            'username' => $value['B'],
                            'nama' => $value['C'],
                            'password' => Hash::make($value['D']),
                            'created_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    // insert data ke database, jika data sudah ada, maka diabaikan
                    UserModel::insertOrIgnore($insert);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            } else {
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
    public function update_profile(Request $request)
{
    // Validasi file upload
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Mendapatkan user yang sedang login
    $user = $request->user();

    // Hapus avatar lama jika ada
    if ($user->avatar) {
        Storage::disk('public')->delete($user->avatar);
    }

    // Simpan avatar baru di folder 'avatars' pada disk 'public'
    $avatarPath = $request->file('avatar')->store('avatars', 'public');

    // Perbarui avatar di database
    $user->update(['avatar' => $avatarPath]);

    return redirect()->back()->with('status', 'Foto profil berhasil diperbarui!');
}


    public function updateinfo(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'nullable|max:20|unique:m_user,username,' . $request->user()->user_id . ',user_id',
                'nama'     => 'nullable|max:100',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false, // respon json, true: berhasil, false: gagal
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors() // menunjukkan field mana yang error
                ]);
            }

            $user = $request->user();

            if ($user) {

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
