<?php

use App\Enums\MutasiSaldoStatusEnum;
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
      Schema::create('mutasi_saldos', function (Blueprint $table) {
         $table->id();
         $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
         $table->string('type');
         $table->string('status')->default(MutasiSaldoStatusEnum::Success);
         $table->integer('amount');
         $table->bigInteger('last_saldo');
         $table->text('description')->nullable();
         $table->string('note')->nullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('mutasi_saldos');
   }
};
