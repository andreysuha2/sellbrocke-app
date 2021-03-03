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
            Route::put("", "UserController@update");
        });
        Route::group([ "prefix" => "merchants", "namespace" => "Merchants\Admin" ], function () {
            Route::get("/", "MerchantController@index");
            Route::group([ "prefix" => "merchant" ], function () {
                Route::group([ "prefix" => "{merchant}" ], function () {
                    Route::put("update-password", "MerchantController@updatePassword");
                });
            });
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
        Route::group([ "prefix" => "defects" ], function () {
            Route::get("", "DefectsController@getDefects");
            Route::get("search", "DefectsController@search");
            Route::group([ "prefix" => "defect" ], function () {
                Route::post("", "DefectsController@createDefect");
                Route::group([ "prefix" => "{defect}" ], function () {
                    Route::get("", "DefectsController@getDefect");
                    Route::put("", "DefectsController@updateDefect");
                    Route::delete("", "DefectsController@deleteDefect");
                });
            });
        });
        Route::group([ "prefix" => "categories" ], function () {
            Route::get("", "CategoriesController@getRootCategories");
            Route::get("search", "CategoriesController@search");
            Route::group([ "prefix" => "category" ], function () {
                Route::post("{category?}", "CategoriesController@createCategory");
                Route::group([ "prefix" => "{category}" ], function () {
                    Route::get("", "CategoriesController@getCategory");
                    Route::put("", "CategoriesController@updateCategory");
                    Route::delete("", "CategoriesController@deleteCategory");
                });
            });
        });
        Route::group([ "prefix" => "devices" ], function () {
            Route::get("", "DevicesController@getDevices");
            Route::get("search", "DevicesController@search");
            Route::group([ "prefix" => "device" ], function() {
               Route::post("", "DevicesController@createDevice");
               Route::group([ "prefix" => "{device}" ], function () {
                   Route::put("", "DevicesController@updateDevice");
                   Route::delete("", "DevicesController@removeDevice");
               });
            });
        });
        Route::group([ "prefix" => "products-grids" ], function () {
            Route::get("", "ProductsGridsController@getProductsGrids");
            Route::group([ "prefix" => "product-grid" ], function () {
                Route::post("", "ProductsGridsController@createProductGrid");
                Route::group([ "prefix" => "{product_grid}" ], function () {
                    Route::get("", "ProductsGridsController@getProductGrid");
                    Route::put("", "ProductsGridsController@updateProductGrid");
                    Route::delete("", "ProductsGridsController@deleteProductGrid");
                });
            });
        });
        Route::group([ "prefix" => "customers" ], function () {
            Route::get("", "CustomersController@getCustomers");
            Route::get("search", "CustomersController@search");
        });
        Route::group([ "prefix" => "orders" ], function() {
            Route::get("", "OrdersController@getOrders");
            Route::get("search", "OrdersController@search");
            Route::group([ "prefix" => "order/{order}" ], function () {
                Route::get("", "OrdersController@getOrder");
                Route::put("status", "OrdersController@updateOrderStatus");
                Route::get("order-reminder", "OrdersController@orderReminder");
            });
        });

        Route::group([ "prefix" => "settings" ], function() {
            Route::get("/{group}", "SettingController@getGroup");
            Route::post("/store", "SettingController@store");
        });

        Route::post('/payment', 'PaymentController@payment');
        Route::post('/payment-check', 'PaymentController@paymentCheck');
    });
});

Route::group([ "middleware" => "auth:api-merchants", 'prefix' => 'merchants', 'namespace' => "Merchants"], function () {
    Route::get("/", "MerchantController@index");
    Route::group([ "prefix" => "customer" ], function () {
        Route::get("/{merchantCustomerId}", "CustomerController@index");
        Route::post("/{merchantCustomerId}", "CustomerController@update");
        Route::get("/{merchantCustomerId}/orders", "OrdersController@getOrdersByCustomerId");
        Route::post("", "CustomerController@store");
        Route::delete("", "CustomerController@delete");
    });
    Route::post("auto-registration-email", "CustomerController@autoRegistrationEmail");

    Route::get("search/{query?}", "MerchantController@search")->where("query", "(.*)");
    Route::group([ "prefix" => "orders/{merchant_customer}" ], function () {
        Route::get("/", "OrdersController@getOrders");
        Route::put("update-status", "OrdersController@updateOrderStatus");
        Route::group([ "prefix" => "order" ], function () {
            Route::post("", "OrdersController@createOrder");
            Route::group([ "prefix" => "{order}" ], function () {
                Route::get("", "OrdersController@getOrder");
            });
        });
    });

    Route::get("settings/{group}", "SettingController@getGroup");
    Route::post("shipment", "OrdersController@setShipmentStatus");
});
