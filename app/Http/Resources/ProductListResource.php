<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
   /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
    */
   public function toArray($request)
   {
      // return parent::toArray($request);

      $pricing = [
         'default_price' => $this->price,
         'discount_type' => 'PERCENT',
         'discount_amount' => 0,
         'is_discount' => false,
      ];

      if ($this->productPromo) {

         $disc = $this->productPromo;

         $pricing['is_discount'] = true;
         $pricing['discount_type'] = $disc->discount_type;
         $pricing['discount_amount'] = $disc->discount_amount;
      }

      if ($this->minPrice) {
         $pricing['default_price'] = $this->minPrice->price;
      }

      return [
         'id'      => $this->id,
         'title'   => mb_convert_encoding($this->title, "UTF-8", "auto"),
         'slug'    => $this->slug,
         'sku'    => $this->sku,
         'status'  =>  $this->status,
         'rating'  =>  $this->reviews_avg_rating ? (float) number_format($this->reviews_avg_rating, 1) : 0,
         'pricing' =>  $pricing,
         'category' => $this->category,
         'assets'  =>  $this->featuredImage,
         'category_id' => $this->category_id,
         'short_description' => mb_convert_encoding($this->short_description, "UTF-8", "auto"),
         'product_type' => $this->product_type,
         'sold' => $this->sold > 0 ? shortNumberPlus($this->sold) . ' terjual' : 0,
         'total_stock' => is_null($this->total_stock) ? $this->stock : $this->total_stock,
         'is_unlimited_stock' => $this->is_unlimited_stock,
      ];
   }
}
