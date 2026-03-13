<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->string('status')->default('draft');
            $table->string('theme')->default('light');
            $table->string('meta_description', 255)->nullable();
            $table->longText('content');
            $table->string('pixel_id')->nullable();
            $table->text('pixel_access_token')->nullable();
            $table->string('pixel_test_event_code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};

