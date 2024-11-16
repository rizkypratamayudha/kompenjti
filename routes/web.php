<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ListPekerjaanMHSController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PekerjanController;
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
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [welcomeController::class, 'index']);

    Route::group(['prefix'=>'profile'], function(){
        Route::get('/edit', [UserController::class, 'profile']);
        Route::post('/update_profile', [UserController::class, 'update_profile']);
        Route::put('/update', [UserController::class, 'updateinfo']);
        Route::put('/update_password', [UserController::class, 'update_password']);
        Route::post('/delete_avatar', [UserController::class, 'deleteAvatar']);
    });

    Route::group(['prefix' => 'level'], function () {
        Route::get('/', [LevelController::class, 'index']);
        Route::post('/list', [LevelController::class, 'list']);
        Route::get('/create_ajax', [LevelController::class, 'create_ajax']);
        Route::post('/ajax', [levelcontroller::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);
        Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax']);
        Route::get('/import', [LevelController::class, 'import']); // ajax form upload excel
        Route::post('/import_ajax', [LevelController::class, 'import_ajax']); // ajax import excel
        Route::get('/export_excel', [LevelController::class, 'export_excel']); // ajax exsport excel
        Route::get('/export_pdf', [LevelController::class, 'export_pdf']);// export pdf
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/list', [UserController::class, 'list']);
        Route::get('/create_ajax', [UserController::class, 'create_ajax']);
        Route::post('/ajax', [UserController::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']);
        Route::get('/{id}/show_ajax', [UserController::class, 'show_ajax']);
        Route::get('/import', [userController::class, 'import']); // ajax form upload excel
        Route::post('/import_ajax', [userController::class, 'import_ajax']); // ajax import excel
        Route::get('/export_excel', [UserController::class, 'export_excel']); // ajax exsport excel
        Route::get('/export_pdf', [UserController::class, 'export_pdf']);// export pdf
    });

    Route::group(['prefix' => 'validasi'], function () {
        Route::get('/', [ValidasiController::class, 'index']);
        Route::post('/list', [ValidasiController::class, 'list']);
        Route::get('/{id}/show_ajax', [ValidasiController::class, 'show_ajax']);
        Route::post('/approve/{id}', [ValidasiController::class, 'approve']);
        Route::post('/decline/{id}', [ValidasiController::class, 'decline']);
    });

    Route::get('/hitung-notif', [ValidasiController::class, 'hitung_notif']);

    Route::group(['prefix' => 'mahasiswa'], function () {
        Route::get('/', [MahasiswaController::class, 'index']);
        Route::post('/list', [MahasiswaController::class, 'list']);
        Route::get('/create_ajax', [MahasiswaController::class, 'create_ajax']);
        Route::post('/ajax', [MahasiswaController::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [MahasiswaController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [MahasiswaController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [MahasiswaController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [MahasiswaController::class, 'delete_ajax']);
        Route::get('/{id}/show_ajax', [MahasiswaController::class, 'show_ajax']);
    });

    Route::group(['prefix'=> 'dosen'], function () {
        Route::get('/', [PekerjanController::class, 'index']);
        Route::get('/create_ajax', [PekerjanController::class, 'create_ajax']);
        Route::post('/ajax', [PekerjanController::class, 'store_ajax']);
        Route::get('/{id}/pekerjaan', [PekerjanController::class, 'enter_pekerjaan']);
        Route::get('/{id}/get-progres', [PekerjanController::class, 'getProgres'])->name('dosen.get-progres');
        Route::get('/{id}/get-pelamaran', [PekerjanController::class, 'getPelamaran']);
        Route::get('{id}/show_ajax',[PekerjanController::class,'show_ajax']);
        Route::put('/{id}/update_ajax', [PekerjanController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [PekerjanController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [PekerjanController::class, 'delete_ajax']);
    });


    Route::group(['prefix'=> 'pekerjaan'], function () {
        Route::get('/', [ListPekerjaanMHSController::class, 'index']);
        Route::post('/list', [ListPekerjaanMHSController::class, 'list']);
        Route::get('/create_ajax', [ListPekerjaanMHSController::class, 'create_ajax']);
        Route::post('/ajax', [ListPekerjaanMHSController::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [ListPekerjaanMHSController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [ListPekerjaanMHSController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [ListPekerjaanMHSController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [ListPekerjaanMHSController::class, 'delete_ajax']);
        Route::get('/{id}/show_ajax', [ListPekerjaanMHSController::class, 'show_ajax']);
        Route::post('/apply',[ListPekerjaanMHSController::class,'apply']);
    });
});
