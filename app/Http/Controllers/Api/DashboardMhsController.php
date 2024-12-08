<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\jamKompenModel;
use Illuminate\Support\Facades\Auth;

class DashboardMhsController extends Controller
{
    // Ambil data user beserta data terkait
  public function index()
{
    // Ambil data user yang sedang login dengan relasi yang diperlukan
    $user = UserModel::with([
        'level',
        'jamKompen',
        'jamKompen.periode',
        'jamKompen.detail_jamKompen.matkul',
        'detailMahasiswa.prodi',
        'detailMahasiswa.periode'
    ])->where('user_id', Auth::id())->first();

    // Ambil data jam kompen untuk user yang sedang login
    $jamkompen = jamKompenModel::with('user', 'detail_jamKompen.matkul')
        ->where('user_id', Auth::id())
        ->get();

    // Return data dalam format JSON
    return response()->json([
        'status' => 'success',
        'data' => [
            'user' => $user,
            'jamkompen' => $jamkompen,
        ]
    ]);
}
}