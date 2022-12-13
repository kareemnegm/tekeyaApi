<?php

namespace App\Http\Resources\User;

use App\Http\Resources\ImageResource;
use App\Models\providerShopBranch;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this);
        // $brnach = providerShopBranch::findOrFail($this->id);

        return [
            'id' => $this->id,
            'shop_name' => $this->shop_name,
            'whatsapp_number' => $this->whatsapp_number ? $this->whatsapp_number : null,
            'facebook_link' => $this->facebook_link ? $this->facebook_link : null,
            'email' => $this->instagram_link ? $this->instagram_link : null,
            'email' => $this->email ? $this->email : null,
            'web_site' => $this->web_site ? $this->web_site : null,
            'shop_logo' => new ImageResource($this->getFirstMedia('shop_logo')) ?? null,
            'shop_cover' => new ImageResource($this->getFirstMedia('shop_cover')) ?? null,

            'delivery_time' => 30,
            'address' => $this->branches->address_details,

            'working_hours_day' => json_decode($this->branches->working_hours_day),
            'distance' => $this->distance > 1 ? round($this->distance, 1) . " K" : round($this->distance * 1000) . " M",


        ];
    }
}
