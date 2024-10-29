<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValidasiController;
use App\Http\Controllers\welcomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::pattern('id', '[0-9]+');
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('register', [RegisterController::class, 'register']);
Route::post('register', [RegisterController::class, 'store']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware(['auth'])->group(function(){
    Route::get('/',[welcomeController::class,'index']);

Route::group(['prefix' => 'level'], function(){
    Route::get('/',[LevelController::class,'index']);
    Route::post('/list',[LevelController::class,'list']);
    Route::get('/create_ajax', [LevelController::class, 'create_ajax']);
    Route::post('/ajax', [levelcontroller::class, 'store_ajax']);
    Route::get('/{id}/edit_ajax',[LevelController::class,'edit_ajax']);
    Route::put('/{id}/update_ajax',[LevelController::class,'update_ajax']);
    Route::get('/{id}/delete_ajax',[LevelController::class,'confirm_ajax']);
    Route::delete('/{id}/delete_ajax',[LevelController::class,'delete_ajax']);
    Route::get('/{id}/show_ajax',[LevelController::class,'show_ajax']);
});


Route::group(['prefix'=>'user'], function(){
    Route::get('/',[UserController::class,'index']);
    Route::post('/list',[UserController::class,'list']);
    Route::get('/create_ajax', [UserController::class, 'create_ajax']);
    Route::post('/ajax', [UserController::class, 'store_ajax']);
    Route::get('/{id}/edit_ajax',[UserController::class,'edit_ajax']);
    Route::put('/{id}/update_ajax',[UserController::class,'update_ajax']);
    Route::get('/{id}/delete_ajax',[UserController::class,'confirm_ajax']);
    Route::delete('/{id}/delete_ajax',[UserController::class,'delete_ajax']);
    Route::get('/{id}/show_ajax',[UserController::class,'show_ajax']);
});

Route::group(['prefix'=>'validasi'], function(){
    Route::get('/',[ValidasiController::class,'index']);
});

});

TOPEK GAMTENG