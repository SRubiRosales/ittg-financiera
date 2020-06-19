<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::resource('clients', 'ClientsController');
    Route::resource('loans', 'LoansController');
    Route::resource('payments', 'PaymentsController')->only(['index', 'show']);
    Route::get('export-client-excel', 'ClientsController@exportExcel')->name('clients.export.excel');
    Route::post('import-client-excel', 'ClientsController@importExcel')->name('clients.import.excel');
});