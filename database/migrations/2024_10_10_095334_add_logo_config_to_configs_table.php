<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   /**
    * Run the migrations.
    */
   public function up(): void
   {
      Schema::table('configs', function (Blueprint $table) {
         $table->unsignedTinyInteger('display_sitename')->default(0);
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::table('configs', function (Blueprint $table) {
         $table->dropColumn('display_sitename');
      });
   }
};
