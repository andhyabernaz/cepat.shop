<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
   /**
    * Run the migrations.
    */
   public function up(): void
   {
      Schema::table('wagateways', function (Blueprint $table) {
         $table->string('default_auth')->nullable();
      });

      Artisan::call('app:wagateay-create-default');
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::table('wagateways', function (Blueprint $table) {
         $table->dropColumn('default_auth');
      });
   }
};
