<?php

namespace App\Http\Resources\User;

use App\Http\Resources\ImageResource;
use App\Http\Resources\ProductDetailsListStockResouce;
use App\Http\Resources\Provider\TagsResource;
use App\Http\Resources\Provider\VariantResource;
use App\Models\CartProduct;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProductResource extends JsonResource
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
            'stock_quantity ' => $this->branchStockQuanttity($request,$this->id),
            'is_published' => $this->is_published,
            'to_donation' => $this->to_donation,

            'variant' => VariantResource::collection($this->variant),

            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name
            ],
            'collection' => [
                'id' => isset($this->collection->id)?$this->collection->id:null,
                'name' => isset($this->collection->name)?$this->collection->name:null
            ],
            'tags' => $this->when(isset($this->tags),  TagsResource::collection($this->tags)),

            'in_cart' => $this->userProductInCart($this->id, auth('user')->check() ? auth('user')->user()->cart->id:null),
            'quantity' => $this->userCartProductQuantity($this->id, auth('user')->check() ? auth('user')->user()->cart->id:null), 

            'product_images' => ImageResource::collection($this->getMedia('product_images')) ?? null,
            'created_at' => $this->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('m-d-Y g:i A') : null,
            'updated_at' => $this->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s', $this->updated_at)->format('m-d-Y g:i A') : null,
            'shop_data' => new ShopDetailsInSingelProductResource($this->shop)

        ];
    }
}
