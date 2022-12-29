<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable

{
    use HasApiTokens, HasFactory, Notifiable , HasRoles;
    
    protected $guard_name = "admin";
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'mobile_no',
        'status'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
    ];

    public function shops()
    {
        return $this->hasMany(ProviderShopDetails::class);
    }

    public function providers()
    {
        return $this->hasMany(Provider::class);
    }
}
