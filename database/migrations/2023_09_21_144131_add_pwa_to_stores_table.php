<?php

use App\Models\Store;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPwaToStoresTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('stores', function (Blueprint $table) {
         $table->string('app_name')->nullable();
         $table->string('download_url')->nullable();
         $table->string('download_desc')->nullable();
      });

      $store = Store::firstOrCreate();

      $store->app_name = $store->name;
      $store->download_desc = 'Berbelanja akan lebih mudah dan cepat dengan menggunakan aplikasi.';
      $store->save();
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::table('stores', function (Blueprint $table) {
         $table->dropColumn('app_name');
         $table->dropColumn('download_url');
         $table->dropColumn('download_desc');
      });
   }
}
