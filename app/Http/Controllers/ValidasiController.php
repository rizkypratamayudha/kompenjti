<?php
namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\PendingRegister;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
    // Find the user in the PendingRegister model along with the 'level' relationship
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

        // Move data to m_user
        UserModel::create([
            'user_id' => $pendingUser->user_id,
            'level_id' => $pendingUser->level_id,
            'prodi_id' => $pendingUser->prodi_id,
            'username' => $pendingUser->username,
            'nama' => $pendingUser->nama,
            'password' => Hash::make($pendingUser->password),  // Ensure the password is hashed
            'email' => $pendingUser->email,
            'no_hp' => $pendingUser->no_hp,
            'angkatan' => $pendingUser->angkatan,
        ]);

        // Delete the user from t_pending_register
        $pendingUser->delete();

        return response()->json(['status' => true, 'message' => 'User approved and moved to m_user.']);
    }

    public function decline(string $id)
    {
        $pendingUser = PendingRegister::find($id);

        if (!$pendingUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Delete the user from t_pending_register
        $pendingUser->delete();

        return response()->json(['status' => true, 'message' => 'User registration declined and removed from pending list.']);
    }
}