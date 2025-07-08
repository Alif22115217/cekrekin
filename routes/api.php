<?php

use App\Http\Controllers\Api\AlatApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Middleware untuk autentikasi jika diperlukan
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Rute API dengan prefix 'v1'
Route::prefix('v1')->group(function () {

    // Rute untuk 'alat'
    Route::prefix('alat')->group(function () {
        Route::get('/', [AlatApiController::class, 'showAllAlat']); // Menampilkan semua alat
        Route::get('/{id}', [AlatApiController::class, 'detail']);  // Menampilkan detail alat berdasarkan ID
        Route::post('/', [AlatApiController::class, 'createAlat']);  // Pastikan ini ada
    });

    // Rute untuk kategori
    Route::prefix('category')->group(function () {
        Route::get('/', [AlatApiController::class, 'showAllCategory']);  // Menampilkan semua kategori
    });
});

// Rute tambahan untuk kalender alat
Route::get('/kalender-alat', function() {
    $order = DB::table('orders')
        ->join('alats', 'alats.id','=','orders.alat_id')
        ->join('payments','payments.id','=','orders.payment_id')
        ->where('orders.status', 2)
        ->where('payments.status', 3)
        ->get(['nama_alat AS title', 'starts AS start', 'ends AS end']);

    return response()->json($order);  // Mengembalikan hasil dalam format JSON
});

// Rute untuk kalender alat berdasarkan ID
Route::get('/kalender-alat/{id}', function($id) {
    $order = DB::table('orders')
        ->join('alats', 'alats.id','=','orders.alat_id')
        ->join('payments','payments.id','=','orders.payment_id')
        ->where('alats.id', $id)
        ->where('orders.status', 2)
        ->where('payments.status', 3)
        ->get(['nama_alat AS title', 'starts AS start', 'ends AS end']);

    return response()->json($order);  // Mengembalikan hasil dalam format JSON
});

