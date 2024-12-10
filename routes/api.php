<?php

use App\Http\Controllers\API\DashboardDsnController;
use App\Http\Controllers\Api\PekerjaanController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\KompetensiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DetailMahasiswaController;
use App\Http\Controllers\Api\DosenBuatPekerjaanController;
use App\Http\Controllers\Api\DashboardMhsController;
use App\Http\Controllers\Api\MahasiswaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/registerWithDetails', [RegisterController::class, 'registerWithDetails']);
Route::post('/loginAPI', [LoginController::class, 'loginAPI']);
Route::get('kompetensi/{user_id}', [KompetensiController::class, 'index']);
Route::post('kompetensi', [KompetensiController::class, 'store']);
Route::get('kompetensi/periode/{user_id}', [KompetensiController::class, 'getPeriodeByUserId']);
Route::get('detail-mahasiswa/user/{user_id}', [DetailMahasiswaController::class, 'getDetailByUserId']);
Route::put('kompetensi/update/{id}', [KompetensiController::class, 'update']);
Route::delete('kompetensi/delete/{id}', [KompetensiController::class, 'destroy']);
Route::get('kompetensi/show/{id}', [KompetensiController::class, 'getKompetensiDetail']);
Route::get('/dosen/pekerjaan/{user_id}', [DosenBuatPekerjaanController::class, 'index']);
Route::post('/dosen/pekerjaan/create', [DosenBuatPekerjaanController::class, 'store']);


Route::middleware(['auth:api'])->group(function () {
    Route::post('/updatePhoto', [ProfileController::class, 'updatePhoto']);
    Route::put('/updatePassword', [ProfileController::class, 'updatePassword']);
    Route::delete('/deleteAvatar', [ProfileController::class, 'deleteAvatar']);

    Route::group(['prefix' => 'dosen'], function(){
        Route::get('/{id}/pelamaran',[PekerjaanController::class,'getPelamaran']);
    });

        Route::group(['prefix' => 'pekerjaan'], function(){
            Route::get('/',[PekerjaanController::class,'index']);
            Route::post('/apply',[PekerjaanController::class,'apply']);
            Route::get('/{id}/get-anggota',[PekerjaanController::class,'get_anggota']);
            Route::post('/approve-pekerjaan',[PekerjaanController::class,'approvePekerjaan']);
            Route::post('/decline-pekerjaan',[PekerjaanController::class,'declinePekerjaan']);
            Route::get('/{id}/getPekerjaanPengerjaan',[PekerjaanController::class,'getPekerjaanPengerjaan']);
            Route::get('/{id}/getNilai',[PekerjaanController::class,'list']);
            Route::post('/{id}/approve-nilai',[PekerjaanController::class,'approve']);
        });

    Route::group(['prefix' => 'mahasiswa'], function () {
        Route::get('/dashboard', [DashboardMhsController::class, 'index']);
        Route::delete('/{id}/hapus',[MahasiswaController::class,'hapus']);
        Route::post('/link',[MahasiswaController::class,'store_link']);
        Route::post('/gambar',[MahasiswaController::class,'store_gambar']);
        Route::post('/file',[MahasiswaController::class,'store_file']);

    });
    Route::group(['prefix' => 'dosen'], function () {
        Route::get('/dashboard', [DashboardDsnController::class, 'index']);
    });
});