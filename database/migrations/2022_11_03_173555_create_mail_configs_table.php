<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_configs', function (Blueprint $table) {
            $table->id();
            $table->string('smtp_host')->default('smtp.googlemail.com');
            $table->string('smtp_port')->default('465');
            $table->string('smtp_encryption')->default('ssl');
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('from_name')->default('Cepatshop');
            $table->string('from_address')->nullable();
            $table->string('mail_admin')->nullable();
            $table->boolean('is_active')->default(0);
        });

        \App\Models\MailConfig::create(); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_configs');
    }
}
