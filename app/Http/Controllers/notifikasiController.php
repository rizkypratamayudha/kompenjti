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
        $notifikasi = notifikasiModel::with('user','pekerjaan','kaprodi')->where('user_id', Auth::id())->orderBy('created_at','desc')->get();
        return view('notifikasi.index',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=> $activeMenu,'notifikasi'=>$notifikasi]);
    }

    public function dibaca($id){
        $notifikasi = notifikasiModel::find($id);

        if($notifikasi){
            $notifikasi->delete();
            return response()->json(['status' => 'success', 'message' => 'Notifikasi ditandai telah dibaca']);
        }

        return response()->json(['status' => 'error', 'message' => 'Notifikasi tidak ditemukan']);
    }
}
