<?php

namespace App\Http\Resources\User;

use App\Http\Resources\PaymentResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

    class MyOrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this->payment->id);
        return [
            "id" => $this->id,
            "order_number" => $this->order_number,
            "total_products" => $this->total_items,
            "total_items" => $this->order_items_sum_quantity,
            "total_shop" => $this->total_shop,
            'order_placed_date' => Carbon::parse($this->date_order_placed)->format('l M-Y g:i A'),
            "payment" => new PaymentResource($this->payment),

            "invoice_info" => [
                "grand_total_price" => $this->invoice->grand_total_price,
                "status" => $this->payment->id == 1 ? 'COD' : 'PAID' ,

            ]

        ];
    }
}
