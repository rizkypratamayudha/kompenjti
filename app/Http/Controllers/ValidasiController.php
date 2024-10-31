<?php
namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\PendingRegister;
use Illuminate\Http\Request;

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
}