<?php

use App\Models\Slider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   /**
    * Run the migrations.
    */
   public function up(): void
   {
      Schema::table('sliders', function (Blueprint $table) {
         $table->string('filepath')->nullable();
      });

      Artisan::call('app:slider-generate-filepath');
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::table('sliders', function (Blueprint $table) {
         $table->dropColumn('filepath');
      });
   }
};
