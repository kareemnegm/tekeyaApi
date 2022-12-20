<?php

namespace App\Http\Resources\User;

use App\Http\Resources\ImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopItemsOrderResource extends JsonResource
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
            'id' => $this->product->id,
            'name' => $this->product->name,
            'description' => $this->product->description,
            'price' => $this->product->price,
            'offer_price' => $this->product->offer_price,
            'quantity' => $this->quantity,
            'variants' => isset($this->variants) ?json_decode($this->variants):null,
            'product_image' => new ImageResource($this->product->getFirstMedia('product_images')) ?? null,
        ];
    }
}
