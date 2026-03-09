<?php

use App\Models\AffiliateConfig;
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
        Schema::create('affiliate_configs', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->integer('ttl')->default(30);
            $table->text('description')->nullable();
        });
        $desc = "Dapatkan penghasilan tambahan dengan bergabung menjadi affiliasi kami, \nhanya dengan membagikan link dan mereferensikan produk kami, anda akan mendapat komisi dari setiap produk yang terjual";
        AffiliateConfig::create([
            'description' => $desc
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_configs');
    }
};
