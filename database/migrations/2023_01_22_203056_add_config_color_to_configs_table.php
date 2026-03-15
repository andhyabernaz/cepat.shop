<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfigColorToConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->string('accent_color', 20)->after('theme_color')->default('#ff8f00');
            $table->string('secondary_color', 20)->after('theme_color')->default('#ec0d0d');
            $table->string('primary_color', 20)->after('theme_color')->default('#15b906');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->dropColumn('accent_color');
            $table->dropColumn('secondary_color');
            $table->dropColumn('primary_color');
        });
    }
}
