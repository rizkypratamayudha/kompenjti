<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ValidasiController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title' => 'Validasi Registrasi',
            'list'=>['Home','Validasi Registrasi']
        ];

        $activeMenu = 'validasi';
        return view('validasi.index',['breadcrumb'=>$breadcrumb,'activeMenu'=>$activeMenu]);
    }
}
