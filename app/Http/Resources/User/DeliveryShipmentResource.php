<?php

namespace App\Http\Resources\User;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryShipmentResource extends JsonResource
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
            'delivery_time' => Carbon::parse($this->deliveryOptions->order->date_order_placed)->addMinute(isset($this->deliveryOptions->shop->deliveryCoverage->average_delivery_time) ?$this->deliveryOptions->shop->deliveryCoverage->average_delivery_time  :30)->format('l M-Y g:i A'),
            "address" =>  new UserOrderAddressResource($this->address),
            "order_user_status" => $this->order_user_status,
            "order_shop_status" => $this->order_shop_status,
        ];
    }
}
