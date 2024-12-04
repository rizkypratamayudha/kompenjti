<?php

use App\Http\Controllers\AdminLihatPekerjaan;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardDosenController;
use App\Http\Controllers\DashboardMahasiswaController;
use App\Http\Controllers\kompetensi_adminController;
use App\Http\Controllers\KompetensiController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ListPekerjaanMHSController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\matkulController;
use App\Http\Controllers\PekerjanController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\riwayatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValidasiController;
use App\Http\Controllers\welcomeController;
use App\Models\kompetensi_adminModel;
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
    Route::get('/', [welcomeController::class, 'index'])->middleware('authorize:ADM');
    Route::get('/dashboardMhs', [DashboardMahasiswaController::class, 'index'])->middleware('authorize:MHS');
    Route::get('/dashboardDos', [DashboardDosenController::class, 'index'])->middleware('authorize:DSN');
    Route::get('/dashboardKap', [welcomeController::class, 'kaprodi'])->middleware('authorize:KPD');
    Route::get('/contact', [welcomeController::class,'contact']);


    Route::group(['prefix'=>'profile'], function(){
        Route::get('/edit', [UserController::class, 'profile']);
        Route::post('/update_profile', [UserController::class, 'update_profile']);
        Route::put('/update', [UserController::class, 'updateinfo']);
        Route::put('/update_password', [UserController::class, 'update_password']);
        Route::post('/delete_avatar', [UserController::class, 'deleteAvatar']);
    });


    // ADMINNN
    Route::group(['prefix' => 'level', 'middleware' => 'authorize:ADM'], function () {
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
    Route::group(['prefix' => 'user', 'middleware' => 'authorize:ADM'], function () {
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
    Route::group(['prefix' => 'validasi','middleware' => 'authorize:ADM'], function () {
        Route::get('/', [ValidasiController::class, 'index']);
        Route::post('/list', [ValidasiController::class, 'list']);
        Route::get('/{id}/show_ajax', [ValidasiController::class, 'show_ajax']);
        Route::post('/approve/{id}', [ValidasiController::class, 'approve']);
        Route::post('/decline/{id}', [ValidasiController::class, 'decline']);
    });
    Route::group(['prefix' => 'mahasiswa', 'middleware' => 'authorize:ADM'], function () {
        Route::get('/', [MahasiswaController::class, 'index']);
        Route::post('/list', [MahasiswaController::class, 'list']);
        Route::get('/create_ajax', [MahasiswaController::class, 'create_ajax']);
        Route::post('/ajax', [MahasiswaController::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [MahasiswaController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [MahasiswaController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [MahasiswaController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [MahasiswaController::class, 'delete_ajax']);
        Route::get('/{id}/show_ajax', [MahasiswaController::class, 'show_ajax']);
        Route::get('/import', [MahasiswaController::class, 'import']); // ajax form upload excel
        Route::post('/import_ajax', [MahasiswaController::class, 'import_ajax']); // ajax import excel
        Route::get('/export_excel', [MahasiswaController::class, 'export_excel']); // ajax exsport excel
        Route::get('/export_pdf', [MahasiswaController::class, 'export_pdf']);// export pdf
    });
    Route::group(['prefix' => 'periode','middleware' => 'authorize:ADM'],function(){
        Route::get('/',[PeriodeController::class,'index']);
        Route::post('/list', [PeriodeController::class, 'list']);
        Route::get('/create_ajax', [PeriodeController::class, 'create_ajax']);
        Route::post('/ajax', [PeriodeController::class, 'store_ajax']);
        Route::get('/{id}/show_ajax',[PeriodeController::class,'show_ajax']);
        Route::get('/{id}/edit_ajax',[PeriodeController::class,'edit_ajax']);
        Route::put('/{id}/update_ajax',[PeriodeController::class,'update_ajax']);
        Route::get('/{id}/confirm_ajax', [PeriodeController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [PeriodeController::class, 'delete_ajax']);
    });
    Route::group(['prefix' => 'kompetensi_admin','middleware' => 'authorize:ADM'],function(){
        Route::get('/',[kompetensi_adminController::class,'index']);
        Route::post('/list', [kompetensi_adminController::class, 'list']);
        Route::get('/create_ajax', [kompetensi_adminController::class, 'create_ajax']);
        Route::post('/ajax', [kompetensi_adminController::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [kompetensi_adminController::class, 'edit_ajax']);
        Route::put('{id}/update_ajax',[kompetensi_adminController::class,'update_ajax']);
        Route::get('/{id}/confirm_ajax', [kompetensi_adminController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [kompetensi_adminController::class, 'delete_ajax']);
        Route::get('/{id}/show_ajax', [kompetensi_adminController::class, 'show_ajax']);
    });
    Route::group(['prefix' => 'matkul','middleware' => 'authorize:ADM'], function () {
        Route::get('/', [MatkulController::class, 'index']);
        Route::post('/list', [MatkulController::class, 'list']);
        Route::get('/create_ajax', [MatkulController::class, 'create_ajax']);
        Route::post('/ajax', [MatkulController::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [MatkulController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [MatkulController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [MatkulController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [MatkulController::class, 'delete_ajax']);
        Route::get('/{id}/show_ajax', [MatkulController::class, 'show_ajax']);
        Route::get('/import', [MatkulController::class, 'import']);
        Route::post('/import_ajax', [MatkulController::class, 'import_ajax']);
        Route::get('/export_excel', [MatkulController::class, 'export_excel']);
        Route::get('/export_pdf', [MatkulController::class, 'export_pdf']);
    });
    Route::group(['prefix' => 'prodi','middleware' => 'authorize:ADM'], function () {
        Route::get('/', [ProdiController::class, 'index']);
        Route::post('/list', [ProdiController::class, 'list']);
        Route::get('/create_ajax', [ProdiController::class, 'create_ajax']);
        Route::post('/ajax', [ProdiController::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [ProdiController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [ProdiController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [ProdiController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [ProdiController::class, 'delete_ajax']);
        Route::get('/{id}/show_ajax', [ProdiController::class, 'show_ajax']);
        Route::get('/import', [ProdiController::class, 'import']);
        Route::post('/import_ajax', [ProdiController::class, 'import_ajax']);
        Route::get('/export_excel', [ProdiController::class, 'export_excel']);
        Route::get('/export_pdf', [ProdiController::class, 'export_pdf']);
    });

    // DOSENNN
    Route::group(['prefix'=> 'dosen'], function () {
        Route::get('/', [PekerjanController::class, 'index'])->name('dosen.index');
        Route::get('/create_ajax', [PekerjanController::class, 'create_ajax']);
        Route::post('/ajax', [PekerjanController::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [PekerjanController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [PekerjanController::class, 'update_ajax']);
        Route::get('/{id}/pekerjaan', [PekerjanController::class, 'enter_pekerjaan']);
        Route::get('/{id}/get-progres', [PekerjanController::class, 'getProgres'])->name('dosen.get-progres');
        Route::get('/{id}/get-pelamaran', [PekerjanController::class, 'getPelamaran']);
        Route::get('/{id}/get-anggota',[PekerjanController::class,'getAnggota']);
        Route::get('{id}/show_ajax',[PekerjanController::class,'show_ajax']);
        Route::get('/{id}/delete_ajax', [PekerjanController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [PekerjanController::class, 'delete_ajax']);
        Route::post('approve-pekerjaan',[PekerjanController::class,'ApprovePekerjaan']);
        Route::post('decline-pekerjaan',[PekerjanController::class,'declinePekerjaan']);
        Route::post('kick-pekerjaan',[PekerjanController::class,'kickPekerjaan']);
        Route::get('/{id}/lihat-pekerjaan',[PekerjanController::class,'lihatPekerjaan']);
        Route::get('/{id}/hitung-notif', [PekerjanController::class, 'hitung_notif_pelamar']);
        Route::get('/{id}/mulai',[PekerjanController::class,'mulai']);
        Route::get('/{id}/show_ajaxdosen',[DashboardDosenController::class,'show_ajax']);
        Route::get('/{id}/mulai',action: [PekerjanController::class,'mulai']);
        Route::get('/masukdosen/{id}/enter-progres',[PekerjanController::class,'enter_progres']);
        Route::post('/{id}/list',[PekerjanController::class,'list']);
        Route::get('{id}/detail-progres',[PekerjanController::class,'detail_progres']);
        Route::post('/approve/{id}',[PekerjanController::class,'approve']);
        Route::post('/decline/{id}',[PekerjanController::class,'decline']);
    });

    // MAHASISWAA
    Route::group(['prefix'=> 'pekerjaan','middleware' => 'authorize:MHS'], function () {
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
        Route::get('/check-if-applied', [ListPekerjaanMHSController::class, 'checkIfApplied'])->name('checkIfApplied');

    });
    Route::group(['prefix' => 'kompetensi','middleware' => 'authorize:MHS'],function(){
        Route::get('/',[KompetensiController::class,'index']);
        Route::post('/list', [KompetensiController::class, 'list']);
        Route::get('/create_ajax', [KompetensiController::class, 'create_ajax']);
        Route::post('/ajax', [KompetensiController::class, 'store']);
        Route::get('/{id}/show_ajax',[KompetensiController::class,'show_ajax']);
        Route::get('/{id}/edit_ajax',[KompetensiController::class,'edit_ajax']);
        Route::put('/{id}/update_ajax',[KompetensiController::class,'update_ajax']);
    });
    Route::group(['prefix'=> 'riwayat','middleware' => 'authorize:MHS'], function () {
        Route::get('/',[riwayatController::class,'index']);
        Route::get('/{id}/riwayatmhs',[riwayatController::class,'enter_pekerjaan']);
        Route::get('/{id}/show_ajax',[riwayatController::class,'show_ajax']);
        Route::get('/riwayatmhs/{id}/enter-progres', [riwayatController::class, 'enter_progres']);
        Route::get('/{id}/link_ajax', [riwayatController::class,'link_ajax']);
        Route::get('{id}/gambar_ajax', [riwayatController::class,'gambar_ajax']);
        Route::get('{id}/file_ajax', [riwayatController::class,'file_ajax']);
        Route::post('/link',[riwayatController::class,'store_link']);
        Route::get('/{id}/hapus_ajax',[riwayatController::class,'hapus_ajax']);
        Route::delete('/{id}/hapus',[riwayatController::class,'hapus']);
        Route::post('/gambar',[riwayatController::class,'store_gambar']);
        Route::post('/file',[riwayatController::class,'store_file']);
    });

    Route::group(['prefix'=> 'lihat','middleware'=> 'authorize:ADM'], function () {
        Route::get('/',[AdminLihatPekerjaan::class,'index']);
        Route::get('/{id}/show_ajax',[AdminLihatPekerjaan::class,'show_ajax']);

    });

    Route::group(['prefix'=> 'admintambah','middleware'=> 'authorize:ADM'], function () {
        Route::get('/',[PekerjanController::class,'index']);
    });

    // NOTIFFF
    Route::get('/hitung-notif', [ValidasiController::class, 'hitung_notif']);
    Route::get('/hitung-notif-pelamar', [ValidasiController::class, 'hitung_notif_pelamar']);
    Route::get('/hitung-notif-pelamar-admin', [ValidasiController::class, 'hitung_notif_pelamar_admin']);
    Route::get('pekerjaan/{id}/get-anggota',[ListPekerjaanMHSController::class,'get_anggota']);
    Route::get('lihat/{id}/get-anggota',[ListPekerjaanMHSController::class,'get_anggota']);
    Route::get('/server-time', function () {
        return response()->json(['server_time' => now()]);
    })->name('server-time');
});
