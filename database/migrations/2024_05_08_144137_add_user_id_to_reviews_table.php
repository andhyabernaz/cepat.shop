<?php

use App\Models\Review;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToReviewsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('reviews', function (Blueprint $table) {
         $table->foreignId('user_id')->nullable();
         $table->string('product_name');
         $table->string('product_varian')->nullable();
      });

      $reviews = Review::all();

      foreach ($reviews as $review) {
         $product = Product::find($review->product_id);
         $review->product_name = $product->title;
         $review->save();
      }
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::table('reviews', function (Blueprint $table) {
         $table->dropColumn('user_id');
         $table->dropColumn('product_name');
         $table->dropColumn('product_varian');
      });
   }
}
