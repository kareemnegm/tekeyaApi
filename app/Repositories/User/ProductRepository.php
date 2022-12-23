<?php

namespace App\Repositories\User;

use App\Models\Product;
use App\Interfaces\User\ProductInterface;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Validator;

class ProductRepository implements ProductInterface
{
    /**
     * Undocumented function
     *
     * @return void
     */
    public function mostPopularProduct($data)
    {
        $q = Product::query();
        $products = Product::where('is_published', 1)->productByDistance($data['latitude'], $data['longitude']);
        return $products;
    }


    /**
     * Undocumented function
     *
     * @return void
     */
    public function productJustForYou($data)
    {
        $q = Product::query();
        $products = Product::where('is_published', 1)->productByDistance($data['latitude'], $data['longitude']);

        return $products;
    }


    /**
     * relatedProducts function
     *
     * @return void
     */
    public function relatedProducts($productId)
    {

        $product = Product::findOrFail($productId);

        $relatedProducts = Product::where('id', '!=', $product->id)->where('is_published', 1)->where('category_id', $product->category_id)->where('shop_id', $product->shop_id)->get();

        return $relatedProducts;
    }

    /**
     * similarProducts function
     *
     * @return void
     */
    public function similarProducts($productId, $request)
    {

        $product = Product::findOrFail($productId);
        $similarProducts = Product::where('category_id', $product->category_id)->where('is_published', 1)->where('shop_id', '!=', $product->shop_id);

    
        if (isset($request['filter']) && $request['filter'] == 'category') {
            $similarProducts->where('category_id', $request['category_id']);
        }
        if ( isset($request['filter']) && $request['filter'] == 'shop') {
            $similarProducts->where('shop_id', $request['shop_id']);
        }
        if (isset($request['sortBy']) && $request['sortBy'] == 'newest') {
            $similarProducts->orderBy('created_at', 'desc');
        }

        if (isset($request['sortBy']) &&$request['sortBy'] == 'alphabetical') {
            $similarProducts->orderBy('name', isset($request['sort']) ? $request['sort'] : 'asc');
        }

        if (isset($request['sortBy']) &&$request['sortBy'] == 'price') {
            $similarProducts->orderBy('price', isset($request['sort']) ? $request['sort'] : 'desc');
        }
        if (isset($request['sortBy']) &&$request['sortBy'] == 'nearest') {

            $similarProducts->productByDistance($request['latitude'], $request['longitude']);
        }
        $collections = $similarProducts->get();

        return $collections;
    }


    public function getVariantsValues($variant_id)
    {
        $product_variants = ProductVariant::findOrFail($variant_id);
        return $product_variants->value;
    }
}
