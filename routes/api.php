<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

*/

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('refresh', [AuthController::class, 'refresh']);
    Route::group(['middleware' => 'auth:api'], function(){
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);//->middleware('isLevelSevenOrMore');
        Route::get('payload', [AuthController::class, 'payload']);//->middleware('isLevelSevenOrMore');
    });
});


Route::group([
    'prefix'     => 'product',
], function() {

    Route::group(['middleware' => ['auth:api', 'checkTypeOfUser:employee']], function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::post('/import', [ProductController::class, 'importFromExcel']);
    });

});
