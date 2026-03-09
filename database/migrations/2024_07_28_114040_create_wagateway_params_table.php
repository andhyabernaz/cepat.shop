<?php

use App\Models\Wagateway;
use App\Models\WagatewayParam;
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
      Schema::create('wagateway_params', function (Blueprint $table) {
         $table->id();
         $table->foreignId('wagateway_id')
            ->references('id')->on('wagateways')->onDelete('cascade')
            ->onUpdate('cascade');
         $table->string('param_type');
         $table->string('param_key')->nullable();
         $table->string('param_value')->nullable();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('wagateway_params');
   }
};
