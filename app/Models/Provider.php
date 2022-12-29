<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Provider extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes , HasRoles;

    protected $dates = ['deleted_at'];
    protected $guard_name = "provider";


    protected $fillable = [
        'email',
        'password',
        'type', //shop or charity
        'mobile',
        'admin_id',
        'fcm_token',

    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function providerShopDetails()
    {
        return $this->hasOne(ProviderShopDetails::class);
    }



    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
    ];
}
