<?php

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

Route::group([ "middleware" => "guest:api", "prefix" => "auth", "namespace" => "Auth" ], function () {
    Route::put("", "AuthenticateController@login");
});

Route::group([ "middleware" => "auth:api" ], function () {
    Route::group([ "prefix" => "auth", "namespace" => "Auth" ], function () {
        Route::put("logout", "AuthenticateController@logout");
        Route::get("check", function () { return response()->json("User OK", 200); });
    });
    Route::group([ "prefix" => "user" ], function () {
        Route::get("", "UserController@index");
    });
});

Route::group([ "middleware" => "auth:api-merchants", 'prefix' => 'merchants', 'namespace' => "Merchants"], function () {
    Route::get("/", "MerchantController@index");
});
