<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(){
        $level = LevelModel::all();
        $user = UserModel::all();
        return view('register.register',['level'=>$level,'user'=>$user]);
    }
}
