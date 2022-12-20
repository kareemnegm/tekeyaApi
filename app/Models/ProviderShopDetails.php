<?php

namespace App\Models;

use App\Traits\FileTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProviderShopDetails extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, FileTrait;

    protected $table = 'provider_shop_details';

    protected $fillable = [
        'shop_name',
        'whatsapp_number',
        'facebook_link',
        'instagram_link', //shop or charity
        'email',
        'web_site',
        'provider_id',
        'admin_id',
        'status',
        'vat'

    ];


    public function deliveryCoverage()
    {
        return $this->hasOne(deliveryCoverage::class,'shop_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function sale()
    {
        return $this->hasMany(Sale::class);
    }
    public function message()
    {
        return $this->hasMany(Message::class);
    }
    public function productsCarts()
    {
        return $this->belongsToMany(Product::class, 'cart_product')->withPivot(['quantity','variants'])->select(['product_id', 'name', 'description', 'price', 'offer_price', 'quantity']);
    }

    public function cart()
    {
        return $this->belongsToMany(Cart::class, 'cart_product', 'provider_shop_details_id')->withPivot(['product_id', 'quantity','variants'])->withTimestamps();
    }
    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsToMany(Category::class, 'category_shops', 'shop_id')->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'shop_id');
    }


    public function branches()
    {
        return $this->hasOne(providerShopBranch::class, 'shop_id');
    }
    /**
     * Undocumented function
     *
     * @param Media $media
     * @return void
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(600)
            ->sharpen(0);
    }


    public function scopeNearestShop($query, $latitude, $longitude, $shop_id ,$distance = null, $unit = "km")
    {
        $distance = 30;
        $constant = $unit == "km" ? 6371 : 3959;

        $haversine = "(
            6371 * acos(
                cos(radians(" . $latitude . "))
                * cos(radians(`latitude`))
                * cos(radians(`longitude`) - radians(" . $longitude . "))
                + sin(radians(" . $latitude . ")) * sin(radians(`latitude`))
            )
        )";


       $nearestBrnach= providerShopBranch::where('shop_id',$shop_id)->select(DB::raw("$haversine AS distance, id as id , name as name,shop_id as shop_id"),'latitude','longitude')
                ->orderby("distance", "asc")
                ->having("distance", "<=", $distance)->first();

        if($nearestBrnach){
            return $query->where('id',$nearestBrnach->shop_id)->select(['*', DB::raw("'{$nearestBrnach->distance}' as distance")]);

        }

      return $query->where('id',null);
    }
}
