<?php

use App\Models\Marketplace;
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
        Schema::create('marketplaces', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('icon_path')->nullable();
            $table->text('url')->nullable();
            $table->unsignedTinyInteger('is_active')->default(0);
            $table->unsignedTinyInteger('is_default')->default(0);
            $table->timestamps();
        });

        Marketplace::createDefault();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplaces');
    }
};
