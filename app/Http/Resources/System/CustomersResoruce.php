<?php

namespace App\Http\Resources\System;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomersResoruce extends JsonResource
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

            'id'=>$this->id,
            "first_name"=>$this->first_name,
            "last_name"=>$this->last_name,
            "mobile_code"=>$this->mobile_code,
            "mobile"=>$this->mobile,
            "area"=>$this->area,
            "orders_count"=>$this->orders_count,
            "orders_moeny"=>round($this->ordersInvoices->sum('grand_total_price'),2),
            'created_at'=> $this->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('m-d-Y g:i A'):null,
            'updated_at'=>$this->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s', $this->updated_at)->format('m-d-Y g:i A'):null,

        ];
    }
}
