<?php

use App\Enums\PaymentServiceEnum;
use App\Models\Config;
use App\Models\PaymentConfig;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\PaymentConfigSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_configs', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->string('name')->unique();
            $table->string('value')->nullable();
        });

        $paymentServices = PaymentServiceEnum::getDefaultConfig();

        foreach ($paymentServices as $payment) {
            foreach ($payment['fields'] as $item) {
                if (PaymentConfig::where('vendor', $payment['vendor'])->where('name', $item['name'])->count() == 0) {
                    $item['vendor'] = $payment['vendor'];
                    PaymentConfig::create($item);
                }
            }
        }

        $config = Config::first();

        $tripay = PaymentServiceEnum::Tripay->value;

        PaymentConfig::where('vendor', $tripay)->where('name', 'tripay_api_key')->update([
            'value' => $config->tripay_api_key
        ]);
        PaymentConfig::where('vendor', $tripay)->where('name', 'tripay_private_key')->update([
            'value' => $config->tripay_private_key
        ]);
        PaymentConfig::where('vendor', $tripay)->where('name', 'tripay_mode')->update([
            'value' => $config->tripay_mode
        ]);
        PaymentConfig::where('vendor', $tripay)->where('name', 'tripay_merchant_id')->update([
            'value' => $config->tripay_merchant_code
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_configs');
    }
};
