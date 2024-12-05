<?php

namespace App\Http\Controllers;

use App\Models\notifikasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class notifikasiController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title' => 'Notifikasi',
            'list' => ['Home','Notifikasi']
        ];

        $page = (object)[
            'title' => 'Notifikasi'
        ];

        $activeMenu = 'notifikasi';
        $notifikasi = notifikasiModel::with('user','pekerjaan','kaprodi')->where('user_id', Auth::id())->get();
        return view('notifikasi.index',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=> $activeMenu,'notifikasi'=>$notifikasi]);
    }
}
