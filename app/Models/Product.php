<?php

namespace App\Models;

use App\Enums\ProductTypeEnum;
use App\Models\Asset;
use App\Models\Category;
use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
   use HasFactory, Slugable;

   protected $guarded = [];

   const PRODUCT_DIGITAL = 'Digital';
   const PRODUCT_DEPOSIT = 'Deposit';

   const SUBSCRIPTION_OPTIONS = [
      ['value' => 'D', 'label' => 'Day'],
      ['value' => 'M', 'label' => 'Month'],
      ['value' => 'Y', 'label' => 'Year'],
   ];

   protected $casts = [
      'status' => 'boolean',
      'aff_is_active' => 'boolean',
      'aff_is_percentage' => 'boolean',
      'category_id' => 'integer',
      'price' => 'integer',
   ];

   public $appends = [
      'short_description', 
      'is_digital_download', 
      'is_digital_video',
      'affiliate_detail',
      'is_unlimited_stock'
   ];

   protected static function boot()
   {
      parent::boot();

      static::creating(function ($model) {
         try {
            $model->sku = Generator::uuid4()->toString();
         } catch (\Exception $e) {
            $model->sku = Str::upper(Str::random(32));
            Log::info($e->getMessage());
         }
      });
   }

   public function title(): Attribute
   {
      return Attribute::make(
         get: fn($value) =>  $value ? mb_convert_encoding($value, "UTF-8", "auto") : ''
      );
   }
   public function description(): Attribute
   {
      return Attribute::make(
         get: fn($value) =>  $value ? mb_convert_encoding($value, "UTF-8", "auto") : ''
      );
   }

   public function getAffiliateDetailAttribute()
   {
      if ($this->aff_is_active) {
         return $this->aff_is_percentage ? $this->aff_amount . '%' : money_format_idr($this->aff_amount);
      }
      return '';
   }
   public function getIsUnlimitedStockAttribute()
   {
      if (in_array($this->product_type, [ProductTypeEnum::DigitalDownload->value, ProductTypeEnum::DigitalVideo->value])) {
         return true;
      }
      return false;
   }
    public function leads()
   {
      return $this->hasMany(Lead::class);
   }
   public function getShortDescriptionAttribute()
   {
      $desc = $this->description;
      return $desc ? mb_convert_encoding(createTeaser($desc), "UTF-8", "auto") : '';
   }
   public function getIsDigitalVideoAttribute()
   {
      return $this->product_type == ProductTypeEnum::DigitalVideo->value;
   }
   public function getIsDigitalDownloadAttribute()
   {
      return $this->product_type == ProductTypeEnum::DigitalDownload->value;
   }

   public function digitalDownloads()
   {
      return $this->hasMany(DigitalDownload::class);
   }
   public function digitalVideos()
   {
      return $this->hasMany(DigitalVideo::class);
   }

   public function licenses()
   {
      return $this->hasMany(License::class);
   }

   public function category()
   {
      return $this->belongsTo(Category::class);
   }
   public function images()
   {
      return $this->morphMany(Asset::class, 'assetable')->orderByDesc('variable');
   }
   public function assets()
   {
      return $this->belongsToMany(Asset::class, 'product_asset', 'product_id', 'asset_id')
         ->withPivot('sort')
         ->orderByPivot('sort');
   }
   public function links()
   {
      return $this->hasMany(ProductLink::class);
   }
   public function minPrice()
   {
      return $this->hasOne(ProductVarian::class)
         ->where('has_subvarian', 0)
         ->whereNotNull('price')
         ->orderBy('price');
   }
   public function maxPrice()
   {
      return $this->hasOne(ProductVarian::class)
         ->where('has_subvarian', 0)
         ->whereNotNull('price')
         ->orderByDesc('price');
   }
   public function featuredImage()
   {
      return $this->belongsToMany(Asset::class, 'product_asset', 'product_id', 'asset_id')
         ->withPivot('sort')
         ->orderByPivot('sort');
   }
   public function reviews()
   {
      return $this->hasMany(Review::class)->latest()->where('is_approved', 1);
   }
   public function allReviews()
   {
      return $this->hasMany(Review::class)->latest();
   }
   public function reviewsLimit()
   {
      return $this->hasMany(Review::class)->take(4);
   }
   public function productRating()
   {
      return $this->hasMany(Review::class)->avg('rating');
   }
   public function promo()
   {
      return $this->belongsToMany(Promo::class, 'product_promos', 'product_id', 'promo_id')
         ->withPivot('discount_type', 'discount_amount')
         ->where('end_date', '>', now());
   }
   public function promoRelations()
   {
      return $this->belongsToMany(Promo::class, 'product_promos', 'product_id', 'promo_id');
   }
   public function promoRelationsRunning()
   {
      return $this->belongsToMany(Promo::class, 'product_promos', 'product_id', 'promo_id')->running();
   }
   public function promoRelationsExpired()
   {
      return $this->belongsToMany(Promo::class, 'product_promos', 'product_id', 'promo_id')->expired();
   }
   public function productPromo()
   {
      return $this->hasOne(ProductPromo::class, 'product_id', 'id');
   }
   public function varians()
   {
      return $this->hasMany(ProductVarian::class)
         ->whereNull('varian_id')
         ->orderBy('price');
   }
   public function varianAttributes()
   {
      return $this->hasMany(ProductVarian::class)
         ->where('has_subvarian', 1)
         ->orderBy('label');
   }
   public function varianItems()
   {
      return $this->hasMany(ProductVarian::class)
         ->where('has_subvarian', 0)
         ->orderBy('price');
   }
   public function varianItemSortByPrice()
   {
      return $this->hasMany(ProductVarian::class)
         ->where('has_subvarian', 0)
         ->orderBy('price');
   }
   public static function clearCache($id = null)
   {
      Cache::forget('categories');
      Cache::forget('products');
      Cache::forget('initial_products');
      Cache::forget('product_promo');

      if ($id) {
         if ($product = Product::find($id)) {
            Cache::forget('product_' . $product->id);
            Cache::forget('product_' . $product->slug);
         }
      }
   }

   public static function updateStock($sku, $qty, $decrement = false)
   {
      $productData = Product::where('sku', $sku)->first();

      if (!$productData) {

         $productData = ProductVarian::where('sku', $sku)->first();
      }

      if ($productData) {

         if ($decrement) {

            $productData->stock -= $qty;
         } else {

            $productData->stock += $qty;
         }

         $productData->save();
      }
   }
}
