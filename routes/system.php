<?php

use Illuminate\Support\Facades\Route;


Route::group(['namespace' => 'GovernmentArea'], function () {
    Route::apiResource('government', 'GovernmentController');
    Route::apiResource('area', 'AreaController');
    Route::get('government_area/{id}', 'AreaController@getAllGovernmentAreas');
});


Route::group(['prefix' => 'admin'], function () {
    /**
     * signup
     */
    Route::post('/signup', 'AuthController@signUp')->withoutMiddleware('auth:admin');
    /**
     * login
     */
    Route::post('/login', 'AuthController@login')->withoutMiddleware('auth:admin');

    Route::apiResource('/', 'AdminController')->except(['update', 'delete', 'show']);
    Route::get('/{id}', 'AdminController@show');
    Route::put('changePassword', 'AuthController@ChangePassword');
    Route::put('profile', 'AdminController@editMyAccount');
    Route::put('deactivate/{id}', 'AdminController@deactivateAdminAccount');
    Route::put('activate/{id}', 'AdminController@activateAdminAccount');

    Route::apiResource('adds', 'AddsController');
});



Route::group(['prefix' => 'provider', 'namespace' => 'Provider'], function () {
    Route::post('/', 'ProviderController@createProvider');
    Route::get('/', 'ProviderController@getAllProviders');
});


Route::group(['prefix' => 'shop', 'namespace' => 'Provider'], function () {
    Route::post('/', 'ShopController@createShop');
    Route::post('/approve/{id}', 'ShopController@approverPendingStores');
    Route::get('/', 'ShopController@getShops');
    Route::put('/suspend/{id}', 'ShopController@suspendShop');
    Route::get('/{id}', 'ShopController@getShopDetails');
    Route::put('/{id}', 'ShopController@updateShopDetails');
    Route::post('/branch', 'ShopController@createBranch');
    Route::get('/branch/{id}', 'ShopController@getBranch');
    Route::get('/{id}/branches', 'ShopController@getBranches');
    Route::put('/branch/{id}', 'ShopController@updateBranch');
    Route::delete('/branch/{id}', 'ShopController@deleteBranch');
});




/**
 * Prodcut
 */
Route::group(['namespace' => 'Provider'], function () {
    Route::apiResource('/product', 'ProductController')->except(['index']);
    Route::delete('/product', 'ProductController@destroy');
    Route::put('/product_publish', 'ProductController@publishAdminProduct');
    Route::put('/move_product_collection', 'ProductController@move_product_from_collection');
    Route::get('shop/{id}/products', 'ProductController@index');
    Route::put('order_product', 'ProductController@orderProduct');
    Route::put('add_products_to_collections', 'ProductController@moveAdminProductFromCollection');


    //  Route::put('/product_remove_collection', 'ProductController@remove_product_from_collection');
});

Route::apiResource('/category', 'CategoryController');
Route::apiResource('/deliveryOption', 'DeliveryOptionController');



/**
 * collections
 */


Route::group(['namespace' => 'Provider'], function () {
    Route::apiResource('collection', 'CollectionController');
    Route::put('collection_rename', 'CollectionController@renameCollection');
    Route::put('collection_status', 'CollectionController@changeStatusCollection');
    Route::post('search/collection', 'CollectionController@collectionSearch');

    Route::apiResource('delivery_coverage', 'DeliveryCoverageController');
});
Route::post('search/category', 'CategoryController@CategorySearch');

//     Route::put('status', 'CollectionController@changeStatusCollection');

// });



Route::get('orders', 'OrderController@ShopOrders');
Route::put('order_shop', 'OrderController@AdminUpdateOrderDeliveryStatus');
Route::get('finance_orders', 'OrderController@financeOrders');
Route::get('finance_statistics', 'OrderController@financeStatistics');

/**
 * Custoemers
 */

Route::group(['prefix' => 'customer', 'namespace' => 'Provider'], function () {
    Route::get('list', 'CustomerController@customerList');
    Route::get('show/{userId}', 'CustomerController@customerDetails');
    Route::get('search', 'CustomerController@customersSearch');

});
