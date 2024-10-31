<?php

namespace App\Http\Controllers;

use App\Models\PendingRegister;
use Illuminate\Http\Request;

class ValidasiController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title' => 'Validasi Registrasi',
            'list'=>['Home','Validasi Registrasi']
        ];

        $page = (object)[
            'title' => 'Page Validasi Registrasi Pengguna'
        ];

        $activeMenu = 'validasi';
        $user = PendingRegister::with('level')->get();
        return view('validasi.index',['breadcrumb'=>$breadcrumb,'activeMenu'=>$activeMenu,'page'=>$page,'user'=>$user]);
    }
}
