<?php

namespace App\Http\Resources\User;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceOrderResource extends JsonResource
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

            "order_id"=>$this->id,

            "order_number"=>$this->order_number,
            
            'date_order_placed' => $this->date_order_placed ? 
            Carbon::createFromFormat('Y-m-d H:i:s', $this->date_order_placed)->format('m-d-Y g:i A') : null,

            'total_product' => $this->total_items,

            'total_shop' => $this->total_shop,

            'note' => $this->note,
    
            'order_items' => PlaceOrderItemsResource::collection($this->orderShops),

            'invoice_order' => New OrderInvoiceResource($this->invoice),

        ];

    }
}
