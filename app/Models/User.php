<?php

namespace App\Models;

use App\Traits\FileTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable  implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, FileTrait ,SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        "gender",
        'password',
        'mobile',
        'government_id',
        'area_id',
        'location',
        'mobile_code',
        'country_code',
        'fcm_token'
    ];

    public function government(){
        return $this->belongsTo(Government::class);
    }


    public function area(){
        return $this->belongsTo(Area::class);
    }

    public function message()
    {
        return $this->hasMany(Message::class);
    }
    public function userAddress()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function ordersInvoices()
    {
        return $this->hasMany(OrderInvoice::class);
    }


    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id', 'id');
    }

    public function userLocation()
    {
        return $this->hasOne(UserLocation::class);
    }

public function registerMediaConversions(Media $media = null): void
{
    $this->addMediaConversion('thumb')
        ->width(400)
        ->height(600)
        ->sharpen(0);
}
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
