<?php

namespace App\Models;

use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia

{
    use HasFactory,InteractsWithMedia,FileTrait;
    protected $fillable = [
        'name',
        'category_id'
    ];

    public function sale()
    {
        return $this->hasOne(Sale::class);
    }

    public function providerShopDetails()
    {
        return $this->belongsToMany(ProviderShopDetails::class,'category_shops');
    }
    public function parent()
    {
        return $this->belongsTo(static::class, 'category_id');
    }
    public function children()
    {
        return $this->hasMany(static::class, 'category_id');
    }
    public function subs()
    {
        return $this->children()->with(['subs']);
    }

    public function shops()
    {
        return $this->belongsToMany(ProviderShopDetails::class, 'category_shops','category_id','shop_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Undocumented function
     *
     * @param Media|null $media
     * @return void
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(600)
            ->sharpen(0);
    }

    
}
