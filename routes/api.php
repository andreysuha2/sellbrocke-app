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

Route::group([ "prefix" => "admin" ], function () {
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
        Route::group([ "prefix" => "merchants", "namespace" => "Merchants\Admin" ], function () {
            Route::get("/", "MerchantController@index");
        });
        Route::group([ "prefix" => "companies" ], function () {
            Route::get("", "CompaniesController@getCompanies");
            Route::get("is-free-slug/{slug}", "CompaniesController@checkFreeSlug");
            Route::group([ "prefix" => "company" ], function () {
                Route::post("", "CompaniesController@createCompany");
                Route::group([ "prefix" => "{company}" ], function () {
                    Route::get("", "CompaniesController@getCompany");
                    Route::put("", "CompaniesController@updateCompany");
                    Route::delete("", "CompaniesController@deleteCompany");
                });
            });
        });
    });
});

Route::group([ "middleware" => "auth:api-merchants", 'prefix' => 'merchants', 'namespace' => "Merchants"], function () {
    Route::get("/", "MerchantController@index");
    Route::group([ "prefix" => "customer" ], function () {
        Route::post("", "CustomerController@store");
        Route::delete("", "CustomerController@delete");
    });
});
