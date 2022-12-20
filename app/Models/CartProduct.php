<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    use HasFactory;
    protected $table = 'cart_product';
    
    protected $fillable = [
        'id',
        'product_id',
        'cart_id',
        'provider_shop_details_id',
        'quantity',
        'variants'

    ];


  
    /**
     * Undocumented function
     *
     * @return void
     */
    public function shop()
    {
        return $this->belongsTo(ProviderShopDetails::class, 'provider_shop_details_id');
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
