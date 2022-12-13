<?php

namespace App\Repositories\User;

use App\Models\Product;
use App\Interfaces\User\ProductInterface;
use App\Models\ProductVariant;

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
        // $products = $q->where('is_published', 1)->orderBy('id', 'ASC')->get();
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
    public function similarProducts($productId)
    {

        $product = Product::findOrFail($productId);

        $similarProducts = Product::where('category_id', $product->category_id)->where('is_published', 1)->where('shop_id', '!=', $product->shop_id)->get();

        return $similarProducts;
    }


    public function getVariantsValues($variant_id)
    {
        $product_variants = ProductVariant::findOrFail($variant_id);
        return $product_variants->value;
    }
}
