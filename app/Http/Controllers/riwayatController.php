<?php

namespace App\Http\Controllers;

use App\Models\PekerjaanModel;
use App\Models\ProgresModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class riwayatController extends Controller
{
    public function index(){
        $breadcrumb = (object) [
            'title' => 'Page Pengerjaan dan Riwayat Pekerjaan',
            'list' => ['Home','Riwayat'],
        ];

        $page = (object)[
            'title' => 'Page Pengerjaan dan Riwayat Pekerjaan',
        ];

        $activeMenu = 'riwayatMhs';
        $tugas = PekerjaanModel::with('detail_pekerjaan','progres')->whereHas('approve', function($query){
            $query->where('user_id',Auth::id());
        })->get();
        return view('riwayatMHS.index',['activeMenu'=> $activeMenu,'page' => $page,'breadcrumb'=>$breadcrumb,'tugas'=>$tugas]);
    }

    public function show_ajax ($id){
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan.persyaratan','detail_pekerjaan.kompetensiDosen.kompetensiAdmin')->where('pekerjaan_id',$id)->first();
        $jumlahProgres = ProgresModel::where('pekerjaan_id',$id)->count();

        return view('riwayatMHS.show_ajax',['pekerjaan'=>$pekerjaan,'jumlahProgres'=>$jumlahProgres,'persyaratan'=>$pekerjaan->detail_pekerjaan->persyaratan ?? collect(),'kompetensi'=>$pekerjaan->detail_pekerjaan->kompetensiDosen ?? collect()]);
    }

    public function enter_pekerjaan($id){
        $breadcrumb = (object)[
            'title' => 'Pekerjaan',
            'list' => ['Home','Pekerjaan']
        ];

        $page = (object)[
            'title' => 'Pekerjaan'
        ];

        $activeMenu = 'riwayatMhs';
        $activeTab = 'progres';
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan', 'progres')->where('pekerjaan_id', $id)->first();
        return view('riwayatMHS.pekerjaan',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu,'activeTab'=>$activeTab,'pekerjaan'=>$pekerjaan]);
    }

    public function enter_progres($id){
        $breadcrumb = (object) [
            'title' => 'Progres',
            'list' => ['Home','Pekerjaan','Progres']
        ];

        $page = (object) [
            'title' => 'Page progres'
        ];

        $activeMenu = 'riwayatMhs';
        $progres = ProgresModel::with('pekerjaan','pekerjaan.detail_pekerjaan','pekerjaan.user')->find( $id );  
        return view('riwayatMHS.progres',['breadcrumb'=>$breadcrumb,'activeMenu'=>$activeMenu,'progres'=>$progres,'page'=>$page]);
    }
}
