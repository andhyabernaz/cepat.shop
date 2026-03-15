<?php

namespace Database\Seeders;

use App\Models\PaymentConfig;
use Illuminate\Database\Seeder;
use App\Enums\PaymentServiceEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
}
