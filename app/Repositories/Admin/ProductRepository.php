<?php

namespace App\Repositories\Admin;

use App\Interfaces\Admin\ProductInterface;
use App\Interfaces\Admin\ProviderInterface;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantValue;
use League\Glide\Manipulators\Encode;

class ProductRepository  implements ProductInterface
{

    /**
     * Get All Shop Collection function
     *
     * @param [type] $projectId
     * @return void
     */
    public function getAllAdminShopProduct($request, $shopID)
    {

        $limit=$request->limit ? $request->limit :10;

        $q = Product::query();

        $q->where('shop_id', $shopID);

        if ($request->is_publish) {
            $is_publish = $request->is_publish === 'true' ? 1 : 0;
            $q->where('is_published', $is_publish);
        }

        if (isset($request->sortBy) && !empty($request->sortBy)) {
            if ($request->sortBy == 'alphabetical') {
                $request->sortBy = 'name';
            } elseif ($request->sortBy == 'date_of_creating') {
                $request->sortBy = 'created_at';
            }
            if (isset($request->sort) && !empty($request->sort)) {
                $collections = $q->orderBy($request->sortBy, $request->sort)->paginate($limit);
            } else {
                $collections = $q->orderBy($request->sortBy, 'desc')->paginate($limit);
            }
        } else {
            $collections = $q->orderBy('order', 'ASC')->paginate($limit);
        }


        return $collections;
    }

    /**
     * getAdminProductById function
     *
     * @param [type] $projectId
     * @return void
     */
    public function getAdminProductById($productId)
    {

        return Product::findOrFail($productId);
    }
    /**
     * deleteAdminShopProduct function
     *
     * @param [type] $projectId
     * @return void
     */
    public function deleteAdminShopProduct($projectIDs)
    {
        Product::destroy($projectIDs);
    }
    /**
     * Create Product  function
     *
     * @param [type] $projectId
     * @return void
     */
    public function createAdminShopProduct(array $productDetails)
    {
        $productDetails['admin_id'] = auth('admin')->user()->id;

        $product = Product::create($productDetails);


        if (isset($productDetails['tags'])) {
            $product->attachTags($productDetails['tags']);
        }

        if (!empty($productDetails['product_images'])) {
            $product->saveFiles($productDetails['product_images'], 'product_images');
        }

        if (!empty($productDetails['variants'])) {
            return $this->productVarints($productDetails['variants'], $product);
        } else {
            return $product;
        }
        
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
     * updateAdminShopProduct function
     *
     * @param [type] $projectId
     * @return void
     */
    public function updateAdminShopProduct($collectionID, array $newDetails)
    {

        {

            $product = Product::findOrFail($collectionID);
    
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


    /**
     * Undocumented function
     *
     * @param array $incomingVariants
     * @param [type] $product
     * @return void
     */
     private function deleteProductVariants(array $incomingVariants, $product)
     {
         $array = [];
 
         foreach ($incomingVariants as $variant => $variantValue) {
             $array[] = $variant;
         }
         $deleted_variants = ProductVariant::where('product_id', $product->id)->whereNotIn('id', $array);
         $deleted_variants->delete();
     }

     /**
      * Undocumented function
      *
      * @param array $variants
      * @return void
      */
     private function updateProductVariantValues(array $variants)
     {
         foreach ($variants as $variant => $variantValue) {
             VariantValue::where('id', $variant)->update($variantValue);
         }
     }
 
 

    /**
     * removeAdminProductCollection function
     *
     * @param [type] $products
     * @return void
     */
    public function adminRemoveProductCollection($collectionId, $products)
    {
        $products = Product::where('collection_id', $collectionId)->whereIn('id', $products)->update(['collection_id' => null]);
    }

    /**
     * moveAdminProductFromCollection function
     *
     * @param [type] $products
     * @param [type] $collection_id
     * @return void
     */
    public function moveAdminProductFromCollection($products, $collection_id)
    {
        $products = Product::whereIn('id', $products)->update(['collection_id' => $collection_id]);
        return $products;
    }

    /**
     * publishAdminProduct function
     *
     * @param [type] $productDetails
     * @return void
     */
    public function publishAdminProduct($productDetails)
    {
        $products = Product::where('id', $productDetails['product_id'])->update(['is_published' => $productDetails['is_published']]);
    }
}
