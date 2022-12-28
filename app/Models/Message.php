<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable =[
        'user_id',
        'shop_id',
        'title',
        'date',
        'day',
        'message',

    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function shop(){
        return $this->belongsTo(ProviderShopDetails::class,'shop_id');
    }

}
