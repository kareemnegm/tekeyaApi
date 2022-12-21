<?php

namespace App\Repositories\User;

use App\Http\Controllers\Controller;
use App\Interfaces\User\ShopInrerface;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\providerShopBranch;
use App\Models\ProviderShopDetails;

class ShopRepository extends Controller implements ShopInrerface
{
    /**
     * Listet Nearts Shop function
     *
     * @param [type] $projectId
     * @return void
     */
    public function nearestShops($request)
    {


        // $latitude = 30.012537910528884;
        // $longitude = 31.290307442198323;
        $q = providerShopBranch::ByDistance($request['latitude'], $request['longitude']);
        return $q;
    }

    /**
     * New Shop Liste function
     *
     * @param [type] $projectId
     * @return void
     */
    public function newShops($request)
    {
        // $latitude = 30.012537910528884;
        // $longitude = 31.290307442198323;
        // dd($request);
        $q = providerShopBranch::ByDistance($request['latitude'], $request['longitude']);
        return $q;
    }

    /**
     * New Shop Liste function
     *
     * @param [type] $projectId
     * @return void
     */
    public function shopsProducts($request)
    {

        if (isset($request['category_id'])) {
            $category = Category::findOrFail($request['category_id']);

            $q = providerShopBranch::ByDistance($request['latitude'],$request['longitude'], $category->shops->pluck('id'));

        } else {
            $q = providerShopBranch::ByDistance($request['latitude'],$request['longitude']);
        }

        return $q;
    }


    /**
     * New Shop Liste function
     *
     * @param [type] $projectId
     * @return void
     */
    public function getProductsShop($request)
    {


        $shop = ProviderShopDetails::findOrFail($request->shop_id);
        $q = $shop->products()->where('is_published',1);


        if(isset($request->category_id)){
            $q->where('category_id',$request->category_id);
        }

        if(isset($request->collection_id)){
            $q->where('collection_id',$request->collection_id);
        }

        $products = $q->orderBy('order', 'ASC')->get();


        return $products;
    }

    /**
     * New Shop Liste function
     *
     * @param [type] $projectId
     * @return void
     */
    public function getShopDetails($request)
    {

        $q = ProviderShopDetails::find($request['shop_id']);
        return $q;
    }


    /**
     * New Shop Liste function
     *
     * @param [type] $projectId
     * @return void
     */
    public function getShopBranches($request)
    {
        $latitude = $request->latitude ? $request->latitude : 30.012537910528884;
        $longitude = $request->longitude ? $request->longitude : 31.290307;
        $shop = ProviderShopDetails::findOrFail($request->shop_id);
        $q = providerShopBranch::ByDistance($latitude, $longitude, array($shop->id))->all();

        return $q;
    }



     /**
     * New Shop Liste function
     *
     * @param [type] $projectId
     * @return void
     */
    public function relatedShops($request,$productId)
    {

        $product = Product::findOrFail($productId);

        $category = Category::findOrFail($product->category_id);

        $shops = providerShopBranch::DistanceBranches($request['latitude'], $request['longitude'], $category->shops->pluck('id'))->unique('shop_id');


        return $shops;
    }


    /**
     * New Shop Liste function
     *
     * @param [type] $projectId
     * @return void
     */
    public function getShopCollections($shop_id)
    {


        $collections=Collection::where('shop_id',$shop_id)->where('is_published',1)->get();

        return $collections;

    }






}
