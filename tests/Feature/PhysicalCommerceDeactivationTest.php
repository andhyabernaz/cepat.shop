<?php

namespace Tests\Feature;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\ProductRequest;
use App\Models\Config;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PhysicalCommerceDeactivationTest extends TestCase
{
    public function test_config_forces_shipping_checkout_flags_to_false(): void
    {
        $config = new Config([
            'can_shipping' => true,
            'can_cod' => true,
            'can_checkout_local' => true,
            'can_checkout_pickup' => true,
            'can_checkout_courier' => true,
            'is_shippable' => true,
        ]);

        $this->assertFalse($config->can_shipping);
        $this->assertFalse($config->can_cod);
        $this->assertFalse($config->can_checkout_local);
        $this->assertFalse($config->can_checkout_pickup);
        $this->assertFalse($config->can_checkout_courier);
        $this->assertFalse($config->is_shippable);
    }

    public function test_order_request_rejects_default_product_type(): void
    {
        $request = new OrderRequest();

        $validator = Validator::make([
            'product_type' => 'Default',
            'customer_name' => 'Test User',
            'customer_phone' => '08123',
            'customer_email' => 'test@example.com',
            'payment_type' => 'PAYMENT_GATEWAY',
            'payment_method' => 'VA',
            'payment_name' => 'BCA',
            'order_items' => [['sku' => 'SKU-1']],
            'order_qty' => 1,
            'order_unique_code' => 0,
            'order_subtotal' => 10000,
            'order_total' => 10000,
            'grand_total' => 10000,
            'payment_fee' => 0,
            'service_fee' => 0,
            'voucher_discount' => 0,
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('product_type', $validator->errors()->messages());
    }

    public function test_product_request_rejects_default_product_type(): void
    {
        $request = new ProductRequest();

        $validator = Validator::make([
            'title' => 'Produk Fisik',
            'product_type' => 'Default',
            'price' => 10000,
            'description' => 'Produk test',
            'aff_amount' => 0,
            'assets' => [['id' => 1]],
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('product_type', $validator->errors()->messages());
    }
}
