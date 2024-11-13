<?php

namespace App\Http\Controllers;

use App\Models\PekerjaanModel;
use Illuminate\Http\Request;

class PekerjanController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title' => 'Buat Pekerjaan',
            'list'=> ['Home','Buat Pekerjaan']
        ];

        $page = (object)[
            'title' => 'Page Buat Pekerjaan'
        ];

        $activeMenu = 'pekerjaan';
        $pekerjaan = PekerjaanModel::all();
        return  view('pekerjaan.index',['breadcrumb' => $breadcrumb,'page'=> $page,'activeMenu'=> $activeMenu,'pekerjaan' => $pekerjaan]);
    }
}
