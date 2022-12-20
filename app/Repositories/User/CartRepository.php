<?php
namespace App\Repositories\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\CartProductResource;
use App\Http\Resources\User\UserCartResource;
use App\Interfaces\User\CartInterface;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\ProviderShopDetails;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartRepository extends Controller implements CartInterface
{

    /**
     * Add Product To Cart function
     *
     * @param [type] $req
     * @return void
     */
    public function addProductsToCart($req)
    {
        $cart_id = Auth::user()->cart->id;
        $product=Product::findOrFail($req['product_id']);

        // $availableStock=$product->stock_quantity;
        $q = CartProduct::where('cart_id',$cart_id)->where('product_id',$req['product_id'])->where('provider_shop_details_id',$req['shop_id']);


        if(!isset($req['variants'])){

        $req['variants']=[];
        $variants=$product->variant;

        foreach($variants as $variant){
            $arr=[];
            $arr=[$variant->name => $variant->value->where('is_default',1)->first()->value];
            $req['variants']=array_merge($req['variants'], $arr);

            }
        }
        if(isset($req['variants'])){
            $productInCart=$q->whereJsonContains('variants',$req['variants'])->first();
        }else{
            $productInCart=$q->first();
        }


        $quantity=isset($productInCart) ? $productInCart->quantity + $req['quantity'] :$req['quantity'];


        // if ($availableStock <  $quantity) {
        //     return $this->errorResponseWithMessage('Out Of Stock',422);
        // }
        if(!$productInCart){
            CartProduct::create([
            'cart_id'=> $cart_id,
            'product_id'=> $req['product_id'],
            'provider_shop_details_id'=> $req['shop_id'],
            'quantity'=> $req['quantity'],
            'variants'=> json_encode($req['variants']),
            ]);
            return $this->successResponse('Product added in cart successfully.');
        }

        elseif($productInCart){
            $productInCart->update(['quantity'=>$productInCart->quantity + $req['quantity']]);
            return $this->successResponse('Product updated in cart successfully.');
       }

    }

    /**
     * IncreaseOrDecreaseProduct function
     *
     * @param [type] $req
     * @return void
     */
    public function IncreaseOrDecreaseProductQuantity($req)
    {
        $cart_id = Auth::user()->cart->id;

        $productInCart = CartProduct::where('cart_id',$cart_id)->
        where('product_id',$req['product_id'])->where('provider_shop_details_id',$req['shop_id'])->
        whereJsonContains('variants',$req['variants'])->first();

        // $product=Product::findOrFail($req['product_id']);
        
        if ($req['quantity'] == 0) {

            $productInCart->delete();


            $getCartItmes= $this->getCartProducts();

            return $this->dataResponse([
                'total_products_price' => $getCartItmes['total_products_price'],
                'total_cart_shops' => $getCartItmes['total_cart_shops'],
                'total_cart_products' => $getCartItmes['total_cart_products']
                ,'cart' => $getCartItmes['cart_itmes']], 'Product removed from cart successfully', 200);



        }elseif($productInCart){

            $productInCart->update(['quantity' => $req['quantity']]);

            $getCartItmes= $this->getCartProducts();

            return $this->dataResponse([
                'total_products_price' => $getCartItmes['total_products_price'],
                'total_cart_shops' => $getCartItmes['total_cart_shops'],
                'total_cart_products' => $getCartItmes['total_cart_products']
                ,'cart' => $getCartItmes['cart_itmes']], 'Product quantity updated successfully', 200);

        }
    }


    /**
     * Undocumented function
     *
     * @param [type] $cart_id
     * @return void
     */
    public function getCartProducts()
    {
        $cart_id = Auth::user()->cart->id;
        $cart  = Cart::findOrFail($cart_id);

        $cartUser = ProviderShopDetails::whereHas('cart',
                    function ($query) use($cart){   $query->where('cart_id', $cart->id);})
                   ->distinct()
                    ->orderBy('created_at','DESC')->get();


                    // dd($cartUser);
            $countShop=count($cartUser);
            // $countProducts=$cart->products()->count();

            $cartProducts = CartProduct::with(['product'])->where('cart_id', $cart_id)->get();

            $toalPrice=$cartProducts->sum(function ($product) {
                return $product->product->order_price*$product->quantity;
            });

       return [
        'cart_itmes'=>UserCartResource::collection($cartUser),
        'total_cart_shops'=>$countShop,
        'total_cart_products'=>$cartProducts->sum('quantity'),
        'total_products_price'=>$toalPrice,
       ];
    }

    /**
     * Undocumented function
     *
     * @param [type] $cart_id
     * @return void
     */
    public function clearShopsFromCarts($req)
    {
        $cart_id = Auth::user()->cart->id;
        $cart  = Cart::findOrFail($cart_id);
        CartProduct::where('cart_id',$cart->id)->where('provider_shop_details_id',$req['shop_id'])->delete();

    }

      /**
     * Undocumented function
     *
     * @param [type] $cart_id
     * @return void
     */
    public function cartItemsCount()
    {
        $cart_id = Auth::user()->cart->id;
        $cart  = Cart::findOrFail($cart_id);
        return CartProduct::where('cart_id',$cart->id)->count();
    }


    /**
     * Undocumented function
     *
     * @param [type] $cart_id
     * @return void
     */
    public function addMultiProductsToCarts($itmes)
    {
        $cart_id = Auth::user()->cart->id;

        foreach($itmes as $req ){

        $productInCart = CartProduct::where('cart_id',$cart_id)->
        where('product_id',$req['product_id'])->where('provider_shop_details_id',$req['shop_id'])->first();


        $product=Product::findOrFail($req['product_id']);
        // $availableStock=$product->stock_quantity;


        // if ($availableStock <  $req['quantity']) {
            // return $this->errorResponseWithMessage('Out Of Stock.',422);

        }if ($req['quantity'] == 0) {
            $productInCart->delete();
            return $this->successResponse('Product removed from cart successfully.');
        }

        // }elseif($availableStock >=  $req['quantity'] && $productInCart){

            $productInCart->update(['quantity' => $req['quantity']]);
            return $this->successResponse('Product quantity updated successfully.');
        // }
    //  }
    }
}
