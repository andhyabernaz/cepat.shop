<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
   /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
    */

   // public static $wrap = 'results';

   public function toArray($request)
   {
      // return parent::toArray($request);

      $pricing = [
         'default_price' =>  intval($this->price),
         'max_price' =>  0,
         'discount_type' => 'PERCENT',
         'discount_amount' => 0,
         'is_discount' => false,
      ];

      if ($this->productPromo) {

         $pricing['is_discount'] = true;
         $pricing['discount_type'] = $this->productPromo->discount_type;
         $pricing['discount_amount'] = intval($this->productPromo->discount_amount);
      }

      if ($this->varianItemSortByPrice->count() > 0) {
         $pricing['default_price'] = intval($this->varianItemSortByPrice[0]->price);
         $pricing['max_price'] = intval($this->varianItemSortByPrice[$this->varianItemSortByPrice->count() - 1]->price);
      }

      return [
         'id' => $this->id,
         'title' => mb_convert_encoding($this->title, "UTF-8", "auto"),
         'pricing' => $pricing,
         'slug' => $this->slug,
         'description' => mb_convert_encoding($this->description, "UTF-8", "auto"),
         'sku' => $this->sku,
         'stock' => intval($this->stock),
         'rating' => $this->reviews_avg_rating ? number_format($this->reviews_avg_rating, 1) : 0,
         'weight' => intval($this->weight),
         'assets' => $this->assets,
         'reviews_count' => $this->reviews_count,
         'varian_items' => $this->varianItemSortByPrice,
         'varian_attributes' => $this->varianAttributes,
         'product_type' => $this->product_type,
         'sold' => $this->sold > 0 ? shortNumberPlus($this->sold) . ' terjual' : 0,
         'is_default_type' => $this->is_default_type,
      ];
   }
}
