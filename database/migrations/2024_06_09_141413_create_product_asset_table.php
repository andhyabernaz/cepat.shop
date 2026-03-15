<?php

use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
   /**
    * Run the migrations.
    */
   public function up(): void
   {
      Schema::create('product_asset', function (Blueprint $table) {
         $table->id();
         $table->foreignId('product_id');
         $table->foreignId('asset_id');
         $table->unsignedTinyInteger('sort')->default(1);
      });

      $products = Product::all();

      $numb = 1;
      foreach ($products as $product) {
         foreach ($product->images as $asset) {
            $product->assets()->attach($asset->id, ['sort' => $numb]);
            $numb++;
            $asset->update([
               'assetable_type' => NULL,
               'assetable_id' => NULL,
            ]);
         }
      }
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('product_asset');
   }
};
