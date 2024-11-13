<?php

use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\KompetensiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SemesterController;

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
Route::get('semester/user/{user_id}', [SemesterController::class, 'getSemesterByUserId']);


Route::middleware(['auth:api'])->group(function(){
    
});
Route::middleware(['auth:api'])->group(function () {
     Route::post('/updatePhoto', [ProfileController::class, 'updatePhoto']);
     Route::put('/updatePassword', [ProfileController::class, 'updatePassword']);
     Route::delete('/deleteAvatar', [ProfileController::class, 'deleteAvatar']);
 });
 
 