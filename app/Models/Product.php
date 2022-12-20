<?php

namespace App\Models;

use App\Traits\FileTrait;
use App\Traits\ProductCartTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\HasTags;

class Product extends Model implements HasMedia
{
    use HasFactory, HasTags, InteractsWithMedia, FileTrait , ProductCartTrait;

    protected $mediaCollection = 'product_images';
    protected $appends = ['Tags', 'order_price'];


    protected $fillable = [
        'name',
        'description',
        'price',
        'offer_price',
        'start_date',
        'end_date',
        'stock_quantity',
        'total_weight',
        'is_published',
        'to_donation',
        'collection_id',
        'category_id',
        'shop_id',
        'order',
        'admin_id'

    ];


    public function getOrderPriceAttribute()
    {
        return $this->offer_price  != 0 ? $this->offer_price : $this->price;
    }

    public function branchStock()
    {
        return $this->hasMany(BranchProductStock::class);
    }


    public function order()
    {
        return $this->belongsToMany(Order::class);
    }

    public function cart()
    {
        return $this->belongsToMany(Cart::class, 'cart_product')->withPivot(['provider_shop_detail_id', 'quantity','variants'])->withTimestamps();
    }


    // public function variant()
    // {
    //     return $this->belongsTo(static::class, 'variant_id');
    // }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function collection()
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }


    /**
     * Undocumented function
     *
     * @return void
     */
    public function bundels()
    {
        return $this->belongsToMany(Bundel::class, 'bundel_products')->withTimestamps();
    }


    public function variant()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function shop()
    {
        return $this->belongsTo(ProviderShopDetails::class, 'shop_id');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function branchStockQuanttity(Request $request,$product_id)
    {
        if (auth('user')->check()) {
            $userLocation = auth('user')->user()->userLocation;
            if ($userLocation) {
                $request['latitude'] = $userLocation->latitude;
                $request['longitude'] = $userLocation->longitude;
            }
        } elseif (isset($request->area_id) && !empty($request->area_id)) {
            $area = Area::findOrFail($request->area_id);
            $request['latitude'] = $area->latitude;
            $request['longitude'] = $area->longitude;
        }


        $latitude = $request->latitude ? $request->latitude : 30.012537910528884;
        $longitude = $request->longitude ? $request->longitude : 31.290307;

         $brnach = providerShopBranch::NearestBranch($latitude, $longitude,$this->id)->first();

         if($brnach){

            $branchStock=BranchProductStock::where('product_id',$product_id)->where('branch_id',$brnach->id)->first();
            return $stock= $branchStock?$branchStock->stock_qty:0;
         }else{
            return $stock=0;
         }

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

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getTagsAttribute()
    {
        return $this->tags()->get();
    }

    public function scopeProductByDistance($query, $latitude, $longitude, $categoryId = null, $distance = null, $unit = "km")
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
    
      return  $query->whereHas('shop.branches', function ($q) use($haversine,$distance) {
             $q->select(DB::raw("$haversine AS distance, id as id , name as name,shop_id as shop_id"),'latitude','longitude')
                ->having("distance", "<=", $distance);
            })->get();
           
    }
    
}
