<?php

use App\Models\Withdrawal;
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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->string('ref_code');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('amount');
            $table->string('target_vendor');
            $table->string('target_account');
            $table->string('target_number');
            $table->string('status')->default(Withdrawal::Pending);
            $table->text('note')->nullable();
            $table->text('reason')->nullable();
            $table->boolean('is_fee')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
