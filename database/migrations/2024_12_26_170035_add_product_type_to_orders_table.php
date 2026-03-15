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
      Schema::table('orders', function (Blueprint $table) {
         $table->string('product_type')->default(Product::PRODUCT_DEFAULT);
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::table('orders', function (Blueprint $table) {
         $table->dropColumn('product_type');
      });
   }
};
