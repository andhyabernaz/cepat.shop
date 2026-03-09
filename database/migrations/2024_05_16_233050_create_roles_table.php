<?php

use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('roles', function (Blueprint $table) {
         $table->id();
         $table->string('name');
         $table->boolean('is_default')->default(false);
      });

      Role::create(['name' => 'Admin', 'is_default' => 1]);
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('roles');
   }
}
