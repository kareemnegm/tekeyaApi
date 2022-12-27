<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\PaymentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_name'=>isset($this->user) ? $this->user->first_name .' '.$this->user->last_name :null,
            'user_id'=>isset($this->user) ? $this->user->id:null,
            'provider_name'=>$this->shop->shop_name,
            'provider_id'=>$this->shop->id,
            'delivery_fees'=>$this->invoice->shipment_fees,
            'promo_code_discount'=>null,
            'total' => $this->invoice->total_invoice,
            'payment' => new PaymentResource($this->order->payment),
            'order_date' => $this->order->date_order_placed,
            'status' => $this->deliveryType->order_shop_status??null,
            'order_id' => $this->order_id,
            'order_shop_id' => $this->id,
            'order_number' => $this->order->order_number,

        ];
    }
}
