<?php

use App\Models\PaymentConfig;
use App\Enums\PaymentServiceEnum;
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
        $paymentServices = PaymentServiceEnum::getDefaultConfig();

        foreach ($paymentServices as $payment) {
            foreach ($payment['fields'] as $item) {
                if (PaymentConfig::where('vendor', $payment['vendor'])->where('name', $item['name'])->count() == 0) {
                    $item['vendor'] = $payment['vendor'];
                    PaymentConfig::create($item);
                }
            }
        }

        PaymentConfig::where('name', 'tripay_mode')->where('value', 'sanbox')->update(['value' => 'sandbox']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
