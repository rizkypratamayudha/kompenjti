<?php

namespace App\Http\Controllers;

use App\Models\PeriodeModel;
use App\Models\UserModel;
use Illuminate\Http\Request;

class alphadosenController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title'=>'Data Mahasiswa Kompensasi',
            'list'=>['Home','Mahasiswa Kompensasi']
        ];

        $page = (object)[
            'title'=>'Daftar Mahasiswa Kompensasi yang terdaftar dalam sistem'
        ];

        $activeMenu = 'alphadosen';
        $user = UserModel::all();
        $periode = PeriodeModel::all();
        return view('alphadosen.index',['activeMenu'=>$activeMenu,'user'=>$user,'periode'=>$periode,'page'=>$page,'breadcrumb'=>$breadcrumb]);
    }

    

}
