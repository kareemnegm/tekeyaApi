<?php

namespace App\Http\Resources\User;

use App\Http\Resources\ImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
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
            'id' => $this->product_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'offer_price' => $this->offer_price,
            'quantity' => $this->quantity,
            'variants' => isset($this->pivot->variants) ? json_decode($this->pivot->variants) :NULL,
            'product_image' => new ImageResource($this->getFirstMedia('product_images')) ?? null,
        ];
    }
}
