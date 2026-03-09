<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderHistoriesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('order_histories', function (Blueprint $table) {
         $table->id();
         $table->foreignId('order_id')->constrained('orders')->onDelete('cascade')->onUpdate('cascade');
         $table->date('date');
         $table->time('time');
         $table->string('description');
         $table->string('city_name')->nullable();
         $table->string('manifest_code')->nullable();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('order_histories');
   }
}
