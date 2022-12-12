<?php

namespace App\Http\Resources\User;

use App\Http\Resources\ImageResource;
use App\Models\CartProduct;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
     


        return [

            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'offer_price' => $this->offer_price,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'stock_quantity' => $this->stock_quantity,
            'to_donation' => $this->to_donation,
            'order' => $this->order,
            'shop' => [
                'id' => $this->shop->id,
                'name' => $this->shop->shop_name,

            ],
            'in_cart' => $this->userProductInCart($this->id, auth('user')->check() ? auth('user')->user()->cart->id:null),
            'quantity' => $this->userCartProductQuantity($this->id, auth('user')->check() ? auth('user')->user()->cart->id:null), 
            'product_image' => new ImageResource($this->getFirstMedia('product_images')) ?? null,
        ];
    }
}
