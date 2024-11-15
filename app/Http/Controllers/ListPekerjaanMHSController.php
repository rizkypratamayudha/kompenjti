<?php

namespace App\Http\Controllers;

use App\Models\PekerjaanModel;
use Illuminate\Http\Request;

class ListPekerjaanMHSController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title' => 'List Pekerjaan',
            'list'=> ['Home','List Pekerjaan']
        ];

        $page = (object)[
            'title' => 'List Pekerjaan yang tersedia'
        ];

        $activeMenu = 'pekerjaan';
        $pekerjaan = PekerjaanModel::with('detail_pekerjaan','progres')->get();
        return  view('pekerjaanMHS.index',['breadcrumb' => $breadcrumb,'page'=> $page,'activeMenu'=> $activeMenu,'tugas' => $pekerjaan]);
    }
}
