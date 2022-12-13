<?php

namespace App\Http\Resources;

use App\Http\Resources\User\NearestBranchResource;
use App\Models\Area;
use App\Models\providerShopBranch;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopCategoryResource extends JsonResource
{
    public function toArray($request)
    {

       
        return [
            'id' => $this->shop->id,
            'shop_name' => $this->shop->shop_name,
            'whatsapp_number' => $this->shop->whatsapp_number ? $this->shop->whatsapp_number : null,
            'facebook_link' => $this->shop->facebook_link ? $this->shop->facebook_link : null,
            'email' => $this->shop->instagram_link ? $this->shop->instagram_link : null,
            'email' => $this->shop->email ? $this->shop->email : null,
            'web_site' => $this->shop->web_site ? $this->shop->web_site : null,
            'shop_logo' => new ImageResource($this->shop->getFirstMedia('shop_logo')) ?? null,
            'shop_cover' => new ImageResource($this->shop->getFirstMedia('shop_cover')) ?? null,
            'delivery_time' => 30,

            'nearest_brnach' =>[
                'id' =>$this->id,
                'name' =>$this->name,
                'latitude'=>$this->latitude,
                'longitude'=>$this->longitude,
                'distance' => $this->distance > 1 ? round($this->distance,1) ." K": round($this->distance *1000)." M",
            ],
            ];
    }
}
