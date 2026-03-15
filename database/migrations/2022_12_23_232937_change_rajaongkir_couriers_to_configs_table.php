<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRajaongkirCouriersToConfigsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('configs', function (Blueprint $table) {
         $table->text('rajaongkir_couriers')->nullable()->change();
      });

      App\Models\Config::query()->update([
         'rajaongkir_couriers' => NULL
      ]);
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::table('configs', function (Blueprint $table) {
         $table->string('rajaongkir_couriers')->nullable()->change();
      });
   }
}
