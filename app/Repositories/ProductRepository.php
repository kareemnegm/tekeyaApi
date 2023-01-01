<?php

namespace App\Repositories;

use App\Interfaces\CollectionInterface;
use App\Interfaces\ProductInterface;
use App\Models\BranchProductStock;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantValue;
use GuzzleHttp\Psr7\Request;

class ProductRepository implements ProductInterface
{

    /**
     * Get All Shop Collection function
     *
     * @param [type] $projectId
     * @return void
     */
    public function getAllShopProduct($request)
    {

        $q = Product::query();

        $limit=isset($request['limit']) ? $request['limit'] : 10;

        $q->where('shop_id', auth('provider')->user()->providerShopDetails->id);

        if ($request->is_published) {
            $is_publish = $request->is_published == 'true' ? 1 : 0;
            $q->where('is_published', $is_publish);
        }

        if (isset($request->in_collection) && $request->in_collection == 'true') {
            $q->where('collection_id','!=',null);
        } elseif (isset($request->in_collection) && $request->in_collection == 'false') {
            $q->where('collection_id',null);
        }

        if (isset($request->sortBy) && isset($request->filter)) {
            $collections = $q->orderBy($request->filter, $request->sortBy)->paginate($limit);
        } else {
            $collections = $q->orderBy('order', 'ASC')->paginate($limit);
        }

        return $collections;
    }
    /**
     * Get All Shop Collection function
     *
     * @param [type] $projectId
     * @return void
     */
    public function getAllShopCollectionProduct($request, $collectionId)
    {
        $limit=isset($request['limit']) ? $request['limit'] : 10;

        $q = Product::query();

        $q->where('collection_id', $collectionId);


        if ($request->is_published) {
            $is_publish = $request->is_published == 'true' ? 1 : 0;
            $q->where('is_published', $is_publish);
        }

        if (isset($request->sortBy) && isset($request->filter)) {

            $collections = $q->orderBy($request->filter, $request->sortBy)->paginate($limit);
        } else {
            $collections = $q->orderBy('order', 'ASC')->paginate($limit);
        }

        return $collections;
    }

    /**
     * Undocumented function
     *
     * @param [type] $projectId
     * @return void
     */
    public function getProductById($collectionID)
    {

        return Product::findOrFail($collectionID);
    }
    /**
     * Undocumented function
     *
     * @param [type] $projectId
     * @return void
     */
    public function deleteShopProduct($projectIDs)
    {
        Product::destroy($projectIDs);
    }
    /**
     * Create Product  function
     *
     * @param [type] $projectId
     * @return void
     */
    public function createShopProduct(array $productDetails)
    {
        $productDetails['shop_id'] = auth('provider')->user()->providerShopDetails->id;

        $product = Product::create($productDetails);


        if (isset($productDetails['tags'])) {
            $product->attachTags($productDetails['tags']);
        }

        if (!empty($productDetails['product_images'])) {
            $product->saveFiles($productDetails['product_images'], 'product_images');
        }
        // if (!empty($productDetails['branches_stock'])) {
        //     $this->branchProductStock($productDetails['branches_stock'], $product->id);
        // }

        if (!empty($productDetails['variants'])) {
            return $this->productVarints($productDetails['variants'], $product);
        } else {
            return $product;
        }
    }

    public function updateShopProduct($collectionID, array $newDetails)
    {

        $product = Product::findOrFail($collectionID);

        $newDetails['shop_id'] = auth('provider')->user()->providerShopDetails->id;
        if (isset($newDetails['variants'])) {

            $this->productVarints($newDetails['variants'], $product);
        }
        if (isset($newDetails['variants_id'])) {
            $this->updateProductVariants($newDetails['variants_id']);
            $incoming_variants = $newDetails['variants_id'];
            $this->deleteProductVariants($incoming_variants, $product);
        }
        if (isset($newDetails['variant_values_id'])) {
            $this->updateProductVariantValues($newDetails['variant_values_id']);
        }
        // if (isset($newDetails['branches_stock'])) {
        //     $this->branchProductStock($newDetails['branches_stock'], $product->id);
        // }
        // if (isset($newDetails['branch_stock_id'])) {
        //     $this->updateBranchProductStock($newDetails['branch_stock_id']);
        //     $incoming_branchStockIds = $newDetails['branch_stock_id'];
        //     $this->deleteProductStockBranch($incoming_branchStockIds, $product);
        // }

        $product->update($newDetails);

        $product->attachTags($newDetails['tags']);
        if (isset($newDetails['tags'])) {
            $product->attachTags($newDetails['tags']);
        }
        if (isset($newDetails['deleted_tags'])) {
            $product->detachTags($newDetails['deleted_tags']);
        }

        if (!empty($newDetails['product_images'])) {
            foreach ($newDetails['product_images'] as $productImage) {
                $product->saveFiles($productImage, 'product_images');
            }
        }

        if (!empty($newDetails['deleted_images'])) {
            foreach ($newDetails['deleted_images'] as $productImage) {
                $product->Media()->where('id', $productImage)->delete();
            }
        }


        return $product;
    }


    private function branchProductStock(array $branchStocks, $product_id)
    {
        foreach ($branchStocks as $branchStock) {
            BranchProductStock::updateOrCreate(
                ['product_id' => $product_id, "branch_id" => $branchStock['branch_id'], 'stock_qty' => $branchStock['stock_qty']],
                ['product_id' => $product_id, "branch_id" => $branchStock['branch_id'], 'stock_qty' => $branchStock['stock_qty']]
            );
        }
    }

    private function updateBranchProductStock(array $branchStocks)
    {
        foreach ($branchStocks as $stockId => $value) {
            BranchProductStock::where('id', $stockId)->update($value);
        }
    }


    /**
     * Undocumented function
     *
     * @param [type] $varients
     * @return array
     */
    public function productsSearch($request)
    {

        $products = Product::where('name', 'like', '%' . $request['search'] . '%')->where('shop_id', $request['shop_id']);
        $products->where('shop_id', auth('provider')->user()->providerShopDetails->id);

        $limit=isset($request['limit']) ? $request['limit'] : 10;

        if ($request->is_published) {
            $is_publish = $request->is_published == 'true' ? 1 : 0;
            $products->where('is_published', $is_publish);
        }

        if (isset($request->in_collection) && $request->in_collection == 'true') {
            $products->where('collection_id','!=',null);
        } elseif (isset($request->in_collection) && $request->in_collection == 'false') {
            $products->where('collection_id',null);
        }
        if (isset($request->sortBy) && isset($request->filter)) {
            $collections = $products->orderBy($request->filter, $request->sortBy)->paginate($limit);
        } else {
            $collections = $products->orderBy('order', 'ASC')->paginate($limit);
        }

        return $collections;
    }



    public function collectionSearch($request)
    {

        $collection = Collection::where('name', 'like', '%' . $request['search'] . '%')->where('shop_id',auth('provider')->user()->providerShopDetails->id);


        $limit=isset($request['limit']) ? $request['limit'] : 10;


        if ($request->is_published) {
            $is_publish = $request->is_published == 'true' ? 1 : 0;

            $collection->where('is_published', $is_publish);
        }

        if (isset($request->sortBy) && isset($request->filter)) {
            $collections = $collection->orderBy($request->filter, $request->sortBy)->paginate($limit);
        } else {
            $collections = $collection->orderBy('order', 'ASC')->paginate($limit);
        }

        return $collections;
    }



    public function productNotInCollectionSearch($request)
    {

        $limit=isset($request['limit']) ? $request['limit']:10;

        $prdoucts = Product::orderBy('order', 'ASC')->where('name', 'like', '%' . $request['search'] . '%')->where('collection_id', null)->where('shop_id', $request['shop_id'])->paginate($limit);
        return $prdoucts;
    }
    /**
     * Undocumented function
     *
     * @param [type] $varients
     * @return array
     */
    private function productVarints(array $varients, $product)
    {
        foreach ($varients as $varient => $variantValue) {
            $productVariant = ProductVariant::updateOrCreate(
                ['product_id' => $product->id, "name" => $varient],
                ["name" => $varient]
            );

            foreach ($variantValue as $values) {
                $productVariant->value()->create($values);
            }
        }
        return $product;
    }




    /**
     * Product Update function
     *
     * @param [type] $projectId
     * @return void
     */


    private function updateProductVariants(array $variants)
    {
        foreach ($variants as $variant => $variantValue) {

            ProductVariant::where('id', $variant)->update(['name' => $variantValue]);
        }
    }

    //removes vairants that are not send in the update product
    private function deleteProductVariants(array $incomingVariants, $product)
    {
        $array = [];

        foreach ($incomingVariants as $variant => $variantValue) {
            $array[] = $variant;
        }
        $deleted_variants = ProductVariant::where('product_id', $product->id)->whereNotIn('id', $array);
        $deleted_variants->delete();
    }

    //removes stock Branches that are not send in the update product
    private function deleteProductStockBranch(array $incomingStockIds, $product)
    {
        $array = [];

        foreach ($incomingStockIds as $id => $value) {

            $array[] = $id;
        }
        $deleted_stocks = BranchProductStock::where('product_id', $product->id)->whereNotIn('id', $array);
        $deleted_stocks->delete();
    }



    private function updateProductVariantValues(array $variants)
    {
        foreach ($variants as $variant => $variantValue) {
            VariantValue::where('id', $variant)->update($variantValue);
        }
    }



    public function remove_product_from_collection($productsId)
    {
        Product::whereIn('id', $productsId)->update(['collection_id' => null]);
    }



    public function move_product_from_collection($products, $collection_id)
    {

        $products = Product::whereIn('id', $products)->update(['collection_id' => $collection_id]);
        return $products;
    }

    public function publishOrUnPublishProduct($productDetails)
    {
        $products = Product::where('id', $productDetails['product_id'])->update(['is_published' => $productDetails['is_published']]);
    }



    /***product variants  */

    public function createProductVariant($variant)
    {
        $product = Product::findOrFail($variant['product_id']);
        $productVariant = $product->variant()->create($variant);
        foreach ($variant['variant_values'] as $values) {
            $productVariant->value()->create($values);
        }
        return;
    }

    public function deleteVariantsFromProduct($variant_id)
    {
        $product_variants = ProductVariant::findOrFail($variant_id);
        $provider = Auth('provider')->user()->providerShopDetails->id;
        $product_id = $product_variants->product->shop_id;
        if ($provider == $product_id) {
            $product_variants->delete();
            return true;
        }
        return false;
    }


    public function deleteVariantValue($value_id)
    {
        $variant_value = VariantValue::findOrFail($value_id);
        $provider = Auth('provider')->user()->providerShopDetails->id;
        $product_id = $variant_value->productVariant->product->shop_id;
        if ($provider == $product_id) {
            $variant_value->delete();
            return true;
        }
        return false;
    }

    public function getVariantsValues($variant_id)
    {
        $product_variants = ProductVariant::findOrFail($variant_id);
        return $product_variants->value;
    }

    /**
     * Undocumented function
     *
     * @param [type] $variant_id
     * @return void
     */
    public function getProductVariants($productId)
    {
        $product = Product::findOrFail($productId);

        return $product->variant;
    }



    public function productStockBranches($product_id)
    {
        $product = Product::findOrFail($product_id);
        return $product->branchStock;
    }
}
