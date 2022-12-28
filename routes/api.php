<?php

use App\Mail\MyTestMail;
use App\Models\Order;
use App\Models\OrderShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

Route::get('/payment', 'Provider\PaymentController@index');
Route::apiResource('/category', 'Category\CategoryController')->except(['store', 'update', 'destroy']);


Route::group(['namespace' => 'Provider\GovernmentArea'], function () {
    Route::apiResource('government', 'GovernmentController');
    Route::apiResource('area', 'AreaController');
    Route::get('government_area/{id}', 'AreaController@getAllGovernmentAreas');
});
Route::apiResource('delivery_option', 'DeliveryOptionController');


Route::group(['prefix' => 'user/support', 'namespace' => 'Message', 'middleware' => 'auth:user'], function () {

    Route::post('send-message', 'MessageController@sendMessage');
});

Route::group(['prefix' => 'provider/message', 'namespace' => 'Message', 'middleware' => 'auth:provider'], function () {

    Route::get('/', 'MessageController@ProviderRetrieveMessages');
});





Route::group(['prefix' => 'provider', 'namespace' => 'Provider'], function () {
    /**
     * signup
     */
    Route::post('/signup', 'AuthController@signUp');
    /**
     * login
     */
    Route::post('/login', 'AuthController@login');
});
// Route::group(['namespace' => 'Provider'], function () {
//     /**
//      * signup
//      */
//     Route::post('/signup', 'AuthController@signUp');
//     /**
//      * login
//      */
//     Route::post('/login', 'AuthController@login');
// });

Route::group(['prefix' => 'user', 'namespace' => 'User'], function () {

    Route::post('update_user_id', function () {

        $orders = Order::get();

        foreach ($orders as $order) {
            $orderShop = OrderShop::where('order_id', $order->id)->update(['user_id' => $order->user_id]);
        }
        $ordersShops = OrderShop::get();

        foreach ($ordersShops as $shop) {
            $shop->invoice->update(['user_id' => $shop->user_id]);
        }
        return 'done';
    });
    /**
     * signup
     */
    Route::post('signup', 'AuthController@signUp');
    /**
     * login
     */
    Route::post('login', 'AuthController@login');

    Route::post('notfication', 'AuthController@testSendNotfiaction');

    Route::post('firebase', 'AuthController@firebaseOtp');


    Route::get('send-mail', function () {



        $details = [
            'title' => 'Tekya.com',
            'body' => 'This is for testing email using smtp'
        ];

        Mail::to('anwarsaeed1@yahoo.com')->send(new MyTestMail($details));

        dd("Email is Sent.");
    });

    /**
     * login & Reigster
     */
    Route::post('authentication', 'AuthController@authentication');

    /**
     * Adss Modules
     */
    Route::get('adds', 'AddsController@index');


    /**
     * Category Apis
     */

    Route::get('main_categories', 'CategoryController@getCategories');
    Route::get('sub_categories', 'CategoryController@getSubCategories');
    Route::get('category_shops', 'CategoryController@categoryShops');
    Route::get('category_products', 'CategoryController@categoryProducts');

    /**
     * Shops Apis
     */

    Route::get('nearest_shops', 'ShopController@nearestShops');
    Route::get('new_shops', 'ShopController@newShops');
    Route::get('shops_products', 'ShopController@shopsProducts');
    Route::get('shop/products', 'ShopController@getProductsShop');
    Route::get('shop', 'ShopController@getShopDetails');
    Route::get('shop/branches', 'ShopController@getShopBranches');
    Route::get('shop/collections', 'ShopController@getShopCollections');




    /**
     * Prdocuts Modules
     */
    Route::get('product/{id}', 'ProductController@showProduct');
    Route::get('products_for_you', 'ProductController@productsForYou');
    Route::get('most_popular_products', 'ProductController@mostPopularProduct');
    Route::get('related_products/{id}', 'ProductController@relatedProducts');
    Route::get('similar_products/{id}', 'ProductController@similarProducts');
    Route::get('related_shops/{id}', 'ShopController@relatedShops');
    Route::get('/variant_values', 'ProductController@getVariantsValues');



    /**
     * SearchKeyWords
     */
    Route::get('search', 'SearchController@search');
});




Route::get('category/{id}/shops', 'Provider\ShopController@getShopByCategoryId');
