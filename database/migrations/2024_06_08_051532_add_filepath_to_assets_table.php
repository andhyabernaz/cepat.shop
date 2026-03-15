<?php

use App\Models\Asset;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
   /**
    * Run the migrations.
    */
   public function up(): void
   {
      Schema::table('assets', function (Blueprint $table) {
         $table->string('filepath')->nullable();
      });

      $assets = Asset::all();

      foreach ($assets as $asset) {
         $asset->update([
            'filepath' => 'upload/images/' . $asset->filename
         ]);
      }
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::table('assets', function (Blueprint $table) {
         $table->dropColumn('filepath');
      });
   }
};
