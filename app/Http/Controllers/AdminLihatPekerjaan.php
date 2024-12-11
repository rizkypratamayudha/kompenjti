<?php

namespace App\Http\Controllers;

use App\Models\PekerjaanModel;
use App\Models\ProgresModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLihatPekerjaan extends Controller
{
    public function index(){
        $breadcrumb = (object) [
            'title' => 'List Pekerjaan',
            'list' => ['Home','List Pekerjaan'],
        ];

        $page = (object)[
            'title'=> 'List Semua Pekerjaan',
        ];

        $activeMenu = 'riwayat';
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan','progres')->get();
        return view('admin.index',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu,'pekerjaan'=>$pekerjaan]);
    }

    public function show_ajax($id)
    {
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan.persyaratan', 'detail_pekerjaan.kompetensiDosen.kompetensiAdmin')->where('pekerjaan_id', $id)->first();
        $jumlahProgres = ProgresModel::where('pekerjaan_id', $id)->count();

        return view('admin.show_ajax', [
            'pekerjaan' => $pekerjaan,
            'jumlahProgres' => $jumlahProgres,
            'persyaratan' => $pekerjaan->detail_pekerjaan->persyaratan ?? collect(),
            'kompetensi' => $pekerjaan->detail_pekerjaan->kompetensiDosen ?? collect(),
        ]);
    }

    public function admintambah(){
        $breadcrumb = (object) [
            'title' => 'Buat Pekerjaan',
            'list' => ['Home','Buat Pekerjaan'],
        ];

        $page = (object)[
            'title'=> 'Buat Pekerjaan',
        ];

        $activeMenu = 'admintambah';
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan', 'progres')->where('user_id', Auth::id())->get();
    }
}
