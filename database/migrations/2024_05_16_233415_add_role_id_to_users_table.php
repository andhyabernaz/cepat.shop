<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleIdToUsersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('users', function (Blueprint $table) {
         $table->foreignId('role_id')->nullable();
      });

      $roleAdmin = Role::where('name', 'Admin')->first();

      $user = User::first();

      if ($user) {
         $user->update([
            'role_id' => $roleAdmin->id
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
      Schema::table('users', function (Blueprint $table) {
         $table->dropColumn('role_id');
      });
   }
}
