<?php

namespace App\Services\Product;

use stdClass;
use App\Models\Promo;
use App\Models\Config;
use App\Models\Product;
use App\Models\Category;
use App\Models\Marketplace;
use App\Enums\ProductTypeEnum;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductListCollection;

class PublicProductService
{
   public function getSingleProduct($slug)
   {

      $product = Cache::remember('product_' . $slug, now()->addHour(), function () use ($slug) {

         return Product::with([
            'assets',
            'varianItemSortByPrice:id,product_id,label,value,price,sku,stock,varian_id,weight',
            'varianAttributes:id,product_id,label,value',
            'productPromo' => function ($query) {
               $query->whereHas('promoActive');
            }
         ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('status', 1)
            ->whereIn('product_type', ProductTypeEnum::getNonPhysicalValues())
            ->where(function ($query) use ($slug) {
               $query->where('slug', $slug)
                  ->orWhere('id', $slug);
            })
            ->groupBy('products.id')
            ->firstOrFail();
      });

      return $product;
   }

   public function getAll($request)
   {

      $config = Cache::remember('filter_product_limit', now()->addMinute(), function () {
         return Config::select('catalog_product_limit', 'catalog_product_sort', 'home_product_limit', 'home_product_sort')->first();
      });

      $per_page = $config->catalog_product_limit ?? 12;
      $order_by = $config->catalog_product_sort ?? 'DESC';

      if ($request->source && $request->source == 'home') {
         $per_page = $config->home_product_limit ?? 12;
         $order_by = $config->home_product_sort ?? 'DESC';
      }

      $instance  = Product::query()
         ->where('status', 1)
         ->whereIn('product_type', ProductTypeEnum::getNonPhysicalValues());

      if ($order_by == 'RANDOM') {
         $instance->inRandomOrder();
      } else {
         $instance->orderBy('id', $order_by);
      }

      if ($request->search) {
         $key = $request->search;
         $instance->where('title', 'like', '%' . $key . '%');
      };

      if ($request->category_id) {

         $cid = $request->category_id;

         if (is_numeric($request->category_id)) {
            $cid = $request->category_id;
         } else {
            $category = Category::where('slug', $request->category_id)->first();
            if ($category) {
               $cid = $category->id;
            }
         }

         $ids = Cache::remember('cids_' . $cid, now()->addMinutes(5), function () use ($cid) {
            return Category::where('id', $cid)->orWhere('category_id', $cid)->select('id')->pluck('id')->toArray();
         });

         $instance->whereIn('category_id', $ids);
      }

      $data = $instance->with(['minPrice', 'assets', 'category:id,title,slug', 'productPromo' => function ($query) {
         $query->whereHas('promoActive');
      }])
         ->withSum('varianItems as total_stock', 'stock')
         ->withAvg('reviews', 'rating')
         ->paginate($per_page)->withQueryString();

      return new ProductListCollection($data);
   }
   public function productRelated($id, $per_page = 8)
   {
      $per_page = max(1, (int) $per_page);

      $cacheKey = 'product_related_' . $id . '_' . $per_page;

      if (Cache::has($cacheKey)) {
         $data = Cache::get($cacheKey);
      } else {

         $product = Product::findOrFail($id);

         $instance  = Product::query()
            ->where('status', 1)
            ->whereIn('product_type', ProductTypeEnum::getNonPhysicalValues());

         if ($product->category_id) {

            $cid = $product->category_id;

            $ids = Cache::remember('cids_' . $cid, now()->addMinutes(15), function () use ($cid) {
               $category = Category::find($cid);
               $ids = [];

               // CHILDS
               if (!$category) {
                  return [$cid];
               }

               if ($category->category_id) {

                  $ids = Category::where('category_id', $category->category_id)->select('id')
                     ->pluck('id')
                     ->toArray();
               } else {
                  $ids = Category::where('category_id', $category->id)->select('id')
                     ->pluck('id')
                     ->toArray();
               }

               array_push($ids, $cid);
               return $ids;
            });

            $instance->whereIn('category_id', $ids);
         } else {

            $searches = explode(' ', $product->title);

            $instance->where(function ($q) use ($searches) {
               $q->where('description', 'like', '%' . $searches[0] . '%');
               array_shift($searches);
               if (count($searches) > 1) {

                  for ($i = 0; $i < count($searches); $i++) {
                     $q->orWhere('title', 'like', '%' . $searches[$i] . '%');
                     if ($i == 3) {
                        break;
                     }
                  }
               }
            });
         }

         $data = $instance->with(['minPrice', 'assets', 'category:id,title,slug', 'productPromo' => function ($query) {
            $query->whereHas('promoActive');
         }])
            ->where('id', '!=', $id)
            ->withSum('varianItems as total_stock', 'stock')
            ->withAvg('reviews', 'rating')
            ->inRandomOrder()
            ->limit($per_page)
            ->get();
         Cache::put($cacheKey, $data, now()->addMinutes(15));
      }


      return new ProductListCollection($data);
   }

   public function getManyInId($pids)
   {
      return Product::with(['minPrice', 'assets', 'category:id,title,slug', 'productPromo' => function ($query) {
         $query->whereHas('promoActive');
      }])
         ->where('status', 1)
         ->whereIn('product_type', ProductTypeEnum::getNonPhysicalValues())
         ->whereIn('id', $pids)
         ->withAvg('reviews', 'rating')
         ->get();
   }

   public function search($key)
   {

      return Product::with(['minPrice', 'assets', 'category:id,title,slug', 'productPromo' => function ($query) {
         $query->whereHas('promoActive');
      }])
         ->where('status', 1)
         ->whereIn('product_type', ProductTypeEnum::getNonPhysicalValues())
         ->where('title', 'like', '%' . $key . '%')
         ->withAvg('reviews', 'rating')
         ->get();
   }

   public function getProductByCategory($request)
   {

      try {

         $id = $request->category_id;
         $offset = $request->offset ?? 0;
         $per_page = $request->per_page ?? 6;
         $order_by = $request->order_by ?? 'DESC';

         $ids = [intval($id)];

         $category = Cache::remember('category-' . $id, now()->addHours(3), function () use ($id) {
            return Category::select('id', 'title', 'slug', 'weight', 'category_id')->where('id', $id)->firstOrFail();
         });

         if ($category && !$category->category_id) {
            $cids = Cache::remember('categories-' . $id, now()->addHours(3), function () use ($id) {
               return Category::where('category_id', $id)->select('id')->pluck('id')->toArray();
            });
            $ids = array_merge($ids, $cids);
         }

         $instance  = Product::query()
            ->where('status', 1)
            ->whereIn('product_type', ProductTypeEnum::getNonPhysicalValues());

         if ($order_by == 'RANDOM') {
            $instance->inRandomOrder();
         } else {
            $instance->orderBy('id', $order_by);
         }

         $data = $instance->with(['minPrice', 'assets', 'category:id,title,slug,category_id', 'productPromo' => function ($query) {
            $query->whereHas('promoActive');
         }])
            ->whereIn('category_id', $ids)
            ->withSum('varianItems as total_stock', 'stock')
            ->withAvg('reviews', 'rating')
            ->take($per_page)
            ->offset($offset)
            ->get();

         return [
            'category' => $category,
            'data' => new ProductListCollection($data),
            'limit' => $per_page,
            'offset' => $offset,
            'total' => $instance->count(),
         ];
      } catch (\Exception $e) {
         throw $e;
      }
   }

   public function getProductPromo()
   {
      return Promo::active()->with(['products' => function ($query) {
         $query->with('minPrice');
         $query->with('assets');
         $query->with('productPromo', function ($q) {
            $q->whereHas('promoActive');
         });
         $query->withSum('varianItems as total_stock', 'stock');
         $query->withAvg('reviews', 'rating');
         $query->where('status', 1);
         $query->whereIn('product_type', ProductTypeEnum::getNonPhysicalValues());
      }])
         ->whereHas('products')
         ->get()->map(function ($item) {

            $promo = new stdClass();
            $promo->id = $item->id;
            $promo->label = $item->label;
            $promo->start_date = $item->start_date;
            $promo->end_date = $item->end_date;

            $promo->products = $item->products->map(function ($product) {
               $totalStock = is_null($product->total_stock) ? $product->stock : $product->total_stock;

               if ($product->is_unlimited_stock) {
                  $totalStock = null;
               }

               return [
                  'id'      => $product->id,
                  'title'   => $product->title,
                  'slug'    => $product->slug,
                  'status'  =>  $product->status,
                  'rating'  =>  $product->reviews_avg_rating ? (float) number_format($product->reviews_avg_rating, 1) : 0,
                  'pricing' =>  $this->setPricing($product),
                  'assets'  =>  $product->assets,
                  'sold' => $product->sold > 0 ? shortNumberPlus($product->sold) . ' terjual' : 0,
                  'total_stock' => $totalStock,
                  'is_unlimited_stock' => $product->is_unlimited_stock
               ];
            });

            return $promo;
         });
   }

   protected function setPricing($product)
   {
      $defaultPrice = $product->price;

      $pricing = [
         'default_price' => $defaultPrice,
         'discount_type' => 'PERCENT',
         'discount_amount' => 0,
         'is_discount' => false,
      ];

      if ($product->productPromo) {

         $disc = $product->productPromo;

         $pricing['is_discount'] = true;
         $pricing['discount_type'] = $disc->discount_type;
         $pricing['discount_amount'] = $disc->discount_amount;
      }

      if ($product->minPrice) {
         $pricing['default_price'] = $product->minPrice->price;
      }

      return $pricing;
   }

   public function productLinks($productId)
   {
      return Marketplace::select(
         'marketplaces.*',
         'product_links.product_url',
      )
         ->join('product_links', 'product_links.marketplace_id', 'marketplaces.id')
         ->where('product_links.product_id', $productId)
         ->where('marketplaces.is_active', 1)
         ->get();
   }

   protected function clearCache()
   {
      Cache::forget('products');
      Cache::forget('initial_products');
      Cache::forget('product_promo');
   }
}
