<?php

use Illuminate\Support\Facades\Route;


/**
 *
 * change password
 */
Route::get('/profile', 'UserController@myProfile');
Route::put('changePassword', 'AuthController@ChangePassword');
Route::put('/profile', 'AuthController@updateProfile');

Route::delete('delete_account', 'UserController@deleteUserAccount');

Route::post('logout', 'AuthController@logout');

/**user  addresses */
Route::post('address', 'UserController@createAddress');
Route::get('address', 'UserController@getAddresses');
Route::get('address/{id}', 'UserController@getAddress');
Route::put('address/{id}', 'UserController@updateAddress');
Route::delete('address/{id}', 'UserController@deleteAddress');
/** end of user  addresses */

Route::post('location', 'UserController@createUserLocation');
Route::get('location', 'UserController@getUserLocation');

Route::put('user_area', 'AuthController@userArea');


Route::group(['prefix' => 'cart'], function () {
    Route::post('/product', 'CartController@addProductsToCart');
    Route::put('product/quantity', 'CartController@IncreaseOrDecreaseProductQuantity');
    Route::get('/', 'CartController@getCartProducts');
    Route::delete('/clear_shops ', 'CartController@clearShopsFromCarts');
    Route::post('/multi_products ', 'CartController@addMultiProductsToCarts');
    Route::get('/cart_items_count ', 'CartController@cartItemsCount');
});


Route::group(['prefix' => 'order'], function () {
    Route::post('order_review', 'OrderController@orderReview');
    Route::post('place_order', 'OrderController@placeOrder');
    Route::get('my_orders', 'OrderController@myOrderList');
    Route::get('order_details', 'OrderController@orderDetiels');
    Route::post('cancel', 'OrderController@cancelOrder');
});
