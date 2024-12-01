<?php

use App\Http\Controllers\Api\PekerjaanController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\KompetensiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DetailMahasiswaController;
use App\Http\Controllers\Api\DosenBuatPekerjaanController;
use App\Http\Controllers\Api\DashboardMhsController;


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


Route::get('/periode', [DashboardMhsController::class, 'getPeriode']);
Route::get('/jam-kompen', [DashboardMhsController::class, 'getJamKompen']);
Route::get('/detail-jam-kompen', [DashboardMhsController::class, 'getDetailJamKompenByUserAndPeriode']);
Route::get('/matkul', [DashboardMhsController::class, 'getMatkul']);

Route::prefix('mahasiswa')->group(function () {
    Route::get('/periode', [DashboardMhsController::class, 'getPeriode']);
    Route::post('/jam-kompen', [DashboardMhsController::class, 'getJamKompen']);
});






Route::middleware(['auth:api'])->group(function () {
    Route::post('/updatePhoto', [ProfileController::class, 'updatePhoto']);
    Route::put('/updatePassword', [ProfileController::class, 'updatePassword']);
    Route::delete('/deleteAvatar', [ProfileController::class, 'deleteAvatar']);


    Route::group(['prefix' => 'pekerjaan'], function(){
        Route::get('/',[PekerjaanController::class,'index']);
        Route::post('/store',[PekerjaanController::class,'store']);
    });
});