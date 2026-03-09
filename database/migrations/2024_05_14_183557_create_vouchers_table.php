<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('vouchers', function (Blueprint $table) {
         $table->id();
         $table->string('name');
         $table->string('voucher_code')->nullable();
         $table->timestamp('start_date')->nullable();
         $table->timestamp('end_date')->nullable();
         $table->string('discount_type');
         $table->integer('discount_amount');
         $table->integer('max_discount_amount')->default(0);
         $table->integer('min_transaction')->default(0);
         $table->integer('usage_quota')->default(0);
         $table->boolean('is_type_shipping')->default(0);
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('vouchers');
   }
}
