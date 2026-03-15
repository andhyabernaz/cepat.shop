<?php

use App\Models\Config;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBiteshipToUserAddressesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('user_addresses', function (Blueprint $table) {
         $table->string('receiver_name');
         $table->string('receiver_phone');
         $table->string('receiver_email')->nullable();
         $table->string('subdistrict');
         $table->string('city_id')->nullable();
         $table->string('city');
         $table->string('province');
         $table->string('postal_code')->nullable();
         $table->string('courier_service')->default(Config::RAJAONGKIR_SERVICE);
         $table->renameColumn('label', 'title');
      });

      $userAddress = UserAddress::withoutGlobalScopes()->whereHas('address')->with('address', 'user')->get();

      foreach ($userAddress as $item) {
         $item->update([
            'receiver_name' => $item->user->name,
            'receiver_phone' => $item->user->phone,
            'receiver_email' => $item->user->email,
            'subdistrict' => $item->address->subdistrict_name,
            'city_id' => $item->address->city_id,
            'city' => $item->address->type . ' ' . $item->address->city,
            'province' => $item->address->province,
         ]);
      }
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::table('user_addresses', function (Blueprint $table) {
         $table->dropColumn('receiver_name');
         $table->dropColumn('receiver_phone');
         $table->dropColumn('receiver_email');
         $table->dropColumn('subdistrict');
         $table->dropColumn('city_id');
         $table->dropColumn('city');
         $table->dropColumn('province');
         $table->dropColumn('postal_code');
         $table->dropColumn('courier_service');
         $table->renameColumn('title', 'label');
      });
   }
}
