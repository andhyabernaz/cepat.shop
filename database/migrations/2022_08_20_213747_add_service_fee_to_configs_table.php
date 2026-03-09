<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceFeeToConfigsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('configs', function (Blueprint $table) {
         $table->bigInteger('service_fee')->default(0);
         $table->string('service_fee_label')->default('Biaya Layanan');
         $table->boolean('is_service_fee')->default(false);
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::table('configs', function (Blueprint $table) {
         $table->dropColumn('service_fee');
         $table->dropColumn('is_service_fee');
      });
   }
}
