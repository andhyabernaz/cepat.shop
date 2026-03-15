<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBiteshipServiceToConfigsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('configs', function (Blueprint $table) {
         $table->text('biteship_apikey')->nullable();
         $table->string('courier_default')->default('Rajaongkir');
         $table->text('biteship_couriers')->nullable();
         $table->text('biteship_warehouse')->nullable();
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
         $table->dropColumn('biteship_apikey');
         $table->dropColumn('courier_default');
         $table->dropColumn('biteship_couriers');
         $table->dropColumn('biteship_warehouse');
      });
   }
}
