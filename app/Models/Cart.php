<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $hidden = ['pivot'];

    protected $fillable = [
        'id',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

   

    public function products()
    {
        return $this->belongsToMany(Product::class,'cart_product')->withPivot(['provider_shop_details_id','quantity','variants'])->withTimestamps();
    }

    public function providerShopDetails(){
        return $this->belongsToMany(ProviderShopDetails::class,'cart_product')->withPivot(['product_id','quantity','variants'])->withTimestamps();
    }
}
