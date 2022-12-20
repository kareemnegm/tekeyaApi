<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_shop_id',
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'unit_total',
        'variants'

    ];




    public function order(){
        return $this->belongsTo(Order::class);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
     public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }


}
