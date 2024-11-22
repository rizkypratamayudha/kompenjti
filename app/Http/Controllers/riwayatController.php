<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $activeMenu = 'riwayat';
    }
}
