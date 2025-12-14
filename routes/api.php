<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingTransactionController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\HospitalSpecialistController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\SpecialistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('token-login', [AuthController::class, 'tokenLogin']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// ======================================================================
// ILMU !!
// ======================================================================
// Cara terbaru dan terbaik dalam menuliskan route di api sini kita
// tidak perlu lagi melakukannya seperti deklarasi per url
// kita cukup menuliskan apiResource() saja itu sudah mewakilkan semua nya
//
// kita bisa cek hal tersebut di >> php artisan route:list <<
// ======================================================================

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('user', [AuthController::class, 'user']);
});

Route::middleware('auth:sanctum','role:manager')->group(function () {
    Route::apiResource('specialist', SpecialistController::class);
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('hospitals', HospitalController::class);

    // route untuk hapus data specialist di RS tersebut
    Route::post('hospitals/{hospital}/specialist', [HospitalSpecialistController::class, 'attach']);
    Route::delete('hospitals/{hospital}/specialist/{specialist}', [HospitalSpecialistController::class, 'detach']);
    Route::apiResource('transactions', BookingTransactionController::class);
    // ILMU
    //
    // Pada route api di sini untuk mengubah status dari transaksi tersebut kita hanya perlu menggunakan >> patch() << saja
    // apa bedanya dengan put ?
    // kalau put kita mengubah semua data form tersebut, hal tersebut lebih cocok apabila update data keseluruhan
    // berbeda dengan patch, kita hanya perlu id nya dengan field tertentu saja tanpa perlu update keseluruhan data
    Route::patch('/transactions/{id}/status', [HospitalSpecialistController::class, 'updateStatus']);
});

Route::middleware('auth:sanctum','role:customer|manager')->group(function () {
    Route::get('specialist', [SpecialistController::class, 'index']);
    Route::get('specialist/{specialist}', [SpecialistController::class, 'show']);

    Route::get('hospitals', [HospitalController::class, 'index']);
    Route::get('hospitals/{hospital}', [HospitalController::class, 'show']);

    Route::get('doctors', [DoctorController::class, 'index']);
    Route::get('doctors/{doctor}', [DoctorController::class, 'show']);
});

Route::middleware('auth:sanctum','role:customer')->group(function () {
    Route::get('/doctors-filter', [DoctorController::class, 'filterBySpecialistAndHospital']);
    Route::get('/doctors/{doctorId}/available-slots', [DoctorController::class, 'availableSlots']);

    Route::get('my-orders', [MyOrderController::class, 'index']);
    Route::post('my-orders', [MyOrderController::class, 'store']);
    Route::get('my-orders/{id}', [MyOrderController::class, 'show']);
});
