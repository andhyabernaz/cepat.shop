<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductDisplayModeToConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->integer('catalog_product_limit')->default(10);
            $table->string('catalog_product_sort')->default('DESC');
            $table->integer('home_product_limit')->default(10);
            $table->string('home_product_sort')->default('DESC');
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
            $table->dromColumn('catalog_product_limit');
            $table->dromColumn('catalog_product_sort');
            $table->dromColumn('home_product_limit');
            $table->dromColumn('home_product_sort');
        });
    }
}
