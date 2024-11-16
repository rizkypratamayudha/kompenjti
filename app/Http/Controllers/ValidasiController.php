<?php

namespace App\Http\Controllers;

use App\Mail\declineMail;
use App\Mail\kirimEmail;
use App\Models\detail_dosenModel;
use App\Models\detail_kaprodiModel;
use App\Models\detail_mahasiswaModel;
use App\Models\LevelModel;
use App\Models\PendingRegister;
use App\Models\PeriodeModel;
use App\Models\ProdiModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

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

        return view('validasi.index', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'page' => $page,
            'level' => $level,
        ]);
    }

    public function list(Request $request)
    {
        $users = PendingRegister::select('user_id', 'level_id', 'username', 'nama', 'email', 'no_hp', 'angkatan', 'prodi_id','periode_id')->with('level', 'prodi','periode');

        if ($request->level_id) {
            $users->where('level_id', $request->level_id);
        }

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('aksi', function ($user) {
                $btn  = '<button onclick="modalAction(\'' . url('/validasi/' . $user->user_id .
                    '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function show_ajax(string $id)
    {

        $user = PendingRegister::with(['level', 'prodi','periode'])->find($id);

        if (!$user) {
            return response()->json(['error' => 'Data yang anda cari tidak ditemukan'], 404);
        }

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
            'password' => $pendingUser->password,
        ]);

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
                'periode_id' => $pendingUser->periode_id,
            ]);
        } elseif ($pendingUser->level_id == 4) {
            detail_kaprodiModel::create([
                'user_id' => $user->user_id,
                'prodi_id' => $pendingUser->prodi_id,
                'email' => $pendingUser->email,
                'no_hp' => $pendingUser->no_hp,

            ]);
        }

        $prodiNama = ProdiModel::getProdiNama($pendingUser->prodi_id);
        $periodeNama = PeriodeModel::getPeriodeNama( $pendingUser->periode_id );

        Mail::to($pendingUser->email)->send(new kirimEmail(['nama' => $pendingUser->nama, 'prodi_id' => $prodiNama, 'angkatan' => $pendingUser->angkatan, 'nim' => $pendingUser->username, 'periode' => $periodeNama]));
        $pendingUser->delete();

        return response()->json(['status' => true, 'message' => 'User approved and moved to respective detail table.']);
    }



    public function decline(Request $request, string $id)
    {
        $pendingUser = PendingRegister::find($id);

        if (!$pendingUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $prodiNama = ProdiModel::getProdiNama($pendingUser->prodi_id);
        $periodeNama = PeriodeModel::getPeriodeNama( $pendingUser->periode_id );
        $alasan = $request->input('reason');
        Mail::to($pendingUser->email)->send(new declineMail(['nama' => $pendingUser->nama, 'prodi_id' => $prodiNama, 'angkatan' => $pendingUser->angkatan, 'nim' => $pendingUser->username,'alasan'=> $alasan, 'periode'=>$periodeNama]));
        $pendingUser->delete();

        return response()->json(['status' => true, 'message' => 'User registration declined and removed from pending list.']);
    }

    public function hitung_notif(){
        $jumlah = PendingRegister::count();
        return ['jumlah' => $jumlah];
    }
}
