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
        Schema::create('digital_videos', function (Blueprint $table) {
            $table->id();
            $table->string('video_title')->default('Video');
            $table->string('video_ratio')->default('ratio-16by9');
            $table->string('video_duration')->nullable();
            $table->text('video_description')->nullable();
            $table->text('video_embed');
            $table->timestamps();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_videos');
    }
};
