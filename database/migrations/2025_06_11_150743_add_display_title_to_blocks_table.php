<?php

use App\Models\Block;
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
        Schema::table('blocks', function (Blueprint $table) {
            $table->boolean('is_show_title')->default(false);
        });

        Block::where('position', 'Banner')->update(['position' => 'Top']);
        Block::where('position', 'Featured')->update(['position' => 'Bottom']);
        Block::where('position', 'Partner')->update(['position' => 'Bottom']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropColumn('is_show_title');
        });
    }
};
