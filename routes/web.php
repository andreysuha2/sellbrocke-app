<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    echo phpinfo();
});

Route::get('/fedex', 'FedExController@shipmentRequest');
Route::get('/ups', 'UPSController@shipmentRequest');
Route::get('/ups/label/{trackingNumber}', 'UPSController@labelRecovery');
Route::get('/ups/cancel/{shipmentIdentificationNumber}', 'UPSController@shipmentCancel');
