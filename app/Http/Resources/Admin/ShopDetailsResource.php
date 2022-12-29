<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ImageResource;
use App\Http\Resources\Provider\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopDetailsResource extends JsonResource
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
            'shop_name' => $this->shop_name,
            'whatsapp_number' => $this->whatsapp_number ? $this->whatsapp_number : null,
            'facebook_link' => $this->facebook_link ? $this->facebook_link : null,
            'instagram_link' => $this->instagram_link ? $this->instagram_link : null,
            'email' => $this->instagram_link ? $this->instagram_link : null,
            'email' => $this->email ? $this->email : null,
            'web_site' => $this->web_site ? $this->web_site : null,
            'shop_logo' => new ImageResource($this->getFirstMedia('shop_logo')) ?? null,
            'shop_cover' => new ImageResource($this->getFirstMedia('shop_cover')) ?? null,
            'category' =>  CategoryResource::collection($this->category),
            'delivery' => $this->branches ? $this->branches()->first()->delivery:null,
            'pick_up' => $this->branches ? $this->branches()->first()->pick_up:null,
            'provider' => new ProviderResource($this->provider),
            'status' => $this->status,
            'is_tekeya_delivery' => $this->is_tekeya_delivery

        ];
    }
}
