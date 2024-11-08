<?php
namespace App\Http\Controllers;

use App\Mail\kirimEmail;
use App\Models\detail_dosenModel;
use App\Models\detail_kaprodiModel;
use App\Models\detail_mahasiswaModel;
use App\Models\LevelModel;
use App\Models\PendingRegister;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ValidasiController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumb = (object)[
            'title' => 'Validasi Registrasi',
            'list' => ['Home', 'Validasi Registrasi']
        ];

        $page = (object)[
            'title' => 'Page Validasi Registrasi Pengguna'
        ];

        $activeMenu = 'validasi';
        $level = LevelModel::all();

        $userQuery = PendingRegister::with('level');

        // Filter by level_id if provided
        if ($request->level_id) {
            $userQuery->where('level_id', $request->level_id);
        }

        $user = $userQuery->get();

        return view('validasi.index', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'page' => $page,
            'user' => $user,
            'level' => $level
        ]);
    }

    public function show_ajax(string $id)
{

    $user = PendingRegister::with(['level','prodi'])->find($id);

    // Check if the user is not found
    if (!$user) {
        return response()->json(['error' => 'Data yang anda cari tidak ditemukan'], 404);
    }

    // Return a Blade view to be rendered in the modal
    return view('validasi.show_ajax', ['user' => $user]);
}

public function approve(string $id)
{
    $pendingUser = PendingRegister::find($id);

    if (!$pendingUser) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Move essential data to m_user
    $user = UserModel::create([
        'user_id' => $pendingUser->user_id,
        'level_id' => $pendingUser->level_id,
        'nama' => $pendingUser->nama,
        'username' => $pendingUser->username,
        'password' => $pendingUser->password,  // Pastikan password di-hash
    ]);

    // Move additional data based on level_id
    if ($pendingUser->level_id == 2) {
        detail_dosenModel::create([
            'user_id' => $user->user_id,
            'prodi_id' => $pendingUser->prodi_id,
            'email' => $pendingUser->email,
            'no_hp' => $pendingUser->no_hp,

        ]);
    } elseif ($pendingUser->level_id == 3) {
        detail_mahasiswaModel::create([
            'user_id' => $user->user_id,
            'prodi_id' => $pendingUser->prodi_id,
            'email' => $pendingUser->email,
            'no_hp' => $pendingUser->no_hp,
            'angkatan' => $pendingUser->angkatan,

        ]);
    } elseif ($pendingUser->level_id == 4) {
        detail_kaprodiModel::create([
            'user_id' => $user->user_id,
            'prodi_id' => $pendingUser->prodi_id,
            'email' => $pendingUser->email,
            'no_hp' => $pendingUser->no_hp,

        ]);
    }

    Mail::to($pendingUser->email)->send(new kirimEmail(['nama'=>$pendingUser->nama]));
    $pendingUser->delete();

    return response()->json(['status' => true, 'message' => 'User approved and moved to respective detail table.']);
}



    public function decline(string $id)
    {
        $pendingUser = PendingRegister::find($id);

        if (!$pendingUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        Mail::to($pendingUser->email)->send(new kirimEmail(['nama'=>$pendingUser->nama]));
        // Delete the user from t_pending_register
        $pendingUser->delete();

        return response()->json(['status' => true, 'message' => 'User registration declined and removed from pending list.']);
    }
}
