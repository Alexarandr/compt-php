<?php

use App\Http\Controllers\CounterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::group(['prefix' => 'counter'], function () {
    Route::post('increment', [CounterController::class, 'increment']);
    Route::get('count', [CounterController::class, 'count']);
    Route::delete('reset', [CounterController::class, 'reset']);
});
