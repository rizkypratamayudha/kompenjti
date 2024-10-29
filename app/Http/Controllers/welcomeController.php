<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class welcomeController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title'=>'Selamat Datang',
            'list'=>['Home','Dashboard']
        ];


    $activeMenu = 'dashboard';

    return view('welcome', ['breadcrumb'=>$breadcrumb,'activeMenu'=>$activeMenu]);
    }

}
