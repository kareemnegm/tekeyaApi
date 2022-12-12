<?php

namespace App\Traits;

use App\Models\Area;
use App\Models\CartProduct;
use Illuminate\Support\Facades\Auth;

trait ProductCartTrait
{


    /**
     * Undocumented function
     *
     * @param [type] $date
     * @return array
     */
    public function userProductInCart(int $product_id, int $cartId = null){
    
        $inCart = CartProduct::where('product_id', $product_id)->where('cart_id', $cartId)->where('provider_shop_details_id', $this->shop_id)->exists();
    
        return $inCart;

   
    }



   /**
     * Undocumented function
     *
     * @param [type] $date
     * @return array
     */
    public function userCartProductQuantity(int $product_id, int $cartId = null){

    
     $cartProductQuantity = CartProduct::where('product_id', $product_id)->where('cart_id', $cartId)->first();
    
            return isset($cartProductQuantity) ? $cartProductQuantity->quantity : null;

        
  
    }


}
