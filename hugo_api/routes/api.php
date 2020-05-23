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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::group([
    'prefix' => 'vehicle',
], function () {
    Route::post('save', 'VehiclesController@registerVehicle');
});

Route::group([
    'prefix' => 'journal',
], function () {
    Route::post('checkin', 'VehiclesController@registerCheckIn');
    Route::post('checkout', 'VehiclesController@registerCheckOut');
});

Route::group([
    'prefix' => 'invoice',
], function () {
    Route::put('closing/{month}', 'InvoiceController@closingPeriod');
    Route::get('get/{plate}/{month}', 'InvoiceController@getInvoice');
    Route::get('report/{month}', 'InvoiceController@createReport');
    Route::put('pay/{plate}/{month}', 'InvoiceController@executePayment');
});