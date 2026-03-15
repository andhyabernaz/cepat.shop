<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Order;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DirectCheckoutOrderTest extends TestCase
{
    private string $previousConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousConnection = config('database.default');

        config([
            'installer.installed' => true,
            'database.connections.checkout_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'database.default' => 'checkout_test',
        ]);

        DB::purge('checkout_test');
        DB::reconnect('checkout_test');

        $this->createSchema();
        $this->seedBaseData();
    }

    protected function tearDown(): void
    {
        DB::disconnect('checkout_test');
        config(['database.default' => $this->previousConnection]);

        parent::tearDown();
    }

    public function test_direct_checkout_creates_order_saves_invoice_and_generates_notifications(): void
    {
        DB::table('products')->insert([
            [
                'id' => 1,
                'title' => 'Produk Alpha',
                'slug' => 'produk-alpha',
                'sku' => 'SKU-ALPHA',
                'price' => 50000,
                'stock' => 5,
                'weight' => 100,
                'status' => 1,
                'product_type' => 'Digital',
            ],
            [
                'id' => 2,
                'title' => 'Produk Beta',
                'slug' => 'produk-beta',
                'sku' => 'SKU-BETA',
                'price' => 25000,
                'stock' => 10,
                'weight' => 50,
                'status' => 1,
                'product_type' => 'Digital',
            ],
        ]);

        DB::table('vouchers')->insert([
            'id' => 9,
            'name' => 'Diskon Checkout',
            'voucher_code' => 'HEMAT10',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'discount_type' => 'fixed',
            'discount_amount' => 10000,
            'max_discount_amount' => 10000,
            'min_transaction' => 30000,
            'is_type_shipping' => 0,
            'usage_quota' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = $this->payload([
            'voucher_id' => 9,
            'order_items' => [
                [
                    'product_id' => 1,
                    'sku' => 'SKU-ALPHA',
                    'quantity' => 2,
                    'price' => 1,
                    'note' => 'Varian utama',
                    'image_url' => 'https://example.test/a.jpg',
                    'product_url' => 'https://example.test/a',
                ],
                [
                    'product_id' => 2,
                    'sku' => 'SKU-BETA',
                    'quantity' => 1,
                    'price' => 1,
                    'note' => 'Tambahan',
                    'image_url' => 'https://example.test/b.jpg',
                    'product_url' => 'https://example.test/b',
                ],
            ],
            'order_unique_code' => 321,
            'order_qty' => 1,
            'order_weight' => 1,
            'order_subtotal' => 1,
            'order_total' => 1,
            'grand_total' => 1,
        ]);

        $response = $this->postJson('/api-public/storeorder', $payload);

        $response->assertStatus(200)->assertJsonPath('success', true);

        $order = Order::query()->with(['items', 'transaction'])->firstOrFail();

        $this->assertTrue(str_starts_with($order->order_ref, 'INV'.now()->format('ym')));
        $this->assertSame('Direct Buyer', $order->customer_name);
        $this->assertSame('081234567890', $order->customer_whatsapp);
        $this->assertSame('buyer@example.test', $order->customer_email);
        $this->assertSame('Jl. Mawar No. 88, Jakarta Selatan', $order->shipping_address);
        $this->assertSame('Kirim via WhatsApp', $order->shipping_courier_name);
        $this->assertSame(3, (int) $order->order_qty);
        $this->assertSame(250, (int) $order->order_weight);
        $this->assertSame(125000, (int) $order->order_subtotal);
        $this->assertSame(321, (int) $order->order_unique_code);
        $this->assertSame(2500, (int) $order->service_fee);
        $this->assertSame(10000, (int) $order->voucher_discount);
        $this->assertSame(117821, (int) $order->order_total);
        $this->assertCount(2, $order->items);
        $this->assertSame(50000, (int) $order->items[0]->price);
        $this->assertSame(25000, (int) $order->items[1]->price);
        $this->assertSame('DIRECT_TRANSFER', $order->transaction->payment_type);
        $this->assertSame('Transfer Bank', $order->transaction->payment_name);

        $this->assertDatabaseHas('order_vouchers', [
            'order_id' => $order->id,
            'voucher_id' => 9,
        ]);

        $this->assertDatabaseHas('messages', [
            'event' => 'order_created',
            'recipient' => 'buyer@example.test',
            'via' => Message::VIA_EMAIL,
        ]);
        $this->assertDatabaseHas('messages', [
            'event' => 'order_created',
            'recipient' => '081234567890',
            'via' => Message::VIA_WHATSAPP,
        ]);
        $this->assertDatabaseHas('messages', [
            'event' => 'order_created',
            'recipient' => 'admin@example.test',
            'via' => Message::VIA_EMAIL,
        ]);
        $this->assertDatabaseHas('messages', [
            'event' => 'order_created',
            'recipient' => '628111111111',
            'via' => Message::VIA_WHATSAPP,
        ]);
    }

    public function test_direct_checkout_rejects_if_stock_has_changed_before_submit(): void
    {
        DB::table('products')->insert([
            'id' => 1,
            'title' => 'Produk Limit',
            'slug' => 'produk-limit',
            'sku' => 'SKU-LIMIT',
            'price' => 40000,
            'stock' => 1,
            'weight' => 100,
            'status' => 1,
            'product_type' => 'Digital',
        ]);

        $payload = $this->payload([
            'order_items' => [
                [
                    'product_id' => 1,
                    'sku' => 'SKU-LIMIT',
                    'quantity' => 2,
                    'price' => 40000,
                    'note' => 'Stok lama di browser',
                    'image_url' => 'https://example.test/limit.jpg',
                    'product_url' => 'https://example.test/limit',
                ],
            ],
        ]);

        $response = $this->postJson('/api-public/storeorder', $payload);

        $response
            ->assertStatus(400)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Stok produk Produk Limit tidak mencukupi.');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('messages', 0);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'order_source' => 'direct_checkout',
            'product_type' => 'Digital',
            'customer_name' => 'Direct Buyer',
            'customer_phone' => '081234567890',
            'customer_email' => 'buyer@example.test',
            'shipping_address' => 'Jl. Mawar No. 88, Jakarta Selatan',
            'shipping_type' => 'DIGITAL',
            'shipping_courier_id' => 'WHATSAPP',
            'shipping_courier_name' => 'Kirim via WhatsApp',
            'shipping_courier_service' => 'Konfirmasi dan pengiriman ke nomor WhatsApp',
            'shipping_cost' => 0,
            'payment_type' => 'DIRECT_TRANSFER',
            'payment_method' => 'BCA',
            'payment_name' => 'Transfer Bank',
            'payment_code' => '1234567890',
            'payment_fee' => 0,
            'order_items' => [],
            'order_qty' => 1,
            'order_weight' => 1,
            'order_unique_code' => 0,
            'service_fee' => 0,
            'order_subtotal' => 1,
            'order_total' => 1,
            'grand_total' => 1,
            'voucher_discount' => 0,
            'shipping_discount' => 0,
            'customer_note' => 'Mohon diproses',
            'shipping_coordinate' => '',
        ], $overrides);
    }

    private function seedBaseData(): void
    {
        DB::table('configs')->insert([
            'id' => 1,
            'order_expired_time' => 24,
            'payment_default' => 'Disabled',
            'is_service_fee' => 1,
            'service_fee' => 2500,
            'is_unique_code' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('stores')->insert([
            'id' => 1,
            'name' => 'Demo Store',
            'email' => 'admin@example.test',
            'phone' => '628111111111',
            'address' => 'Jakarta',
        ]);

        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'phone' => '628111111111',
            'role_id' => 1,
        ]);

        DB::table('notification_templates')->insert([
            [
                'event' => 'order_created',
                'label' => 'Order Created Customer Email',
                'role' => 'Customer',
                'subject' => 'Order Created',
                'template' => 'Invoice {{ invoice_number }}',
                'via' => Message::VIA_EMAIL,
                'sort' => 1,
            ],
            [
                'event' => 'order_created',
                'label' => 'Order Created Customer WA',
                'role' => 'Customer',
                'subject' => 'Order Created',
                'template' => 'Invoice {{ invoice_number }}',
                'via' => Message::VIA_WHATSAPP,
                'sort' => 2,
            ],
            [
                'event' => 'order_created',
                'label' => 'Order Created Admin Email',
                'role' => 'Admin',
                'subject' => 'Order Created',
                'template' => 'Invoice {{ invoice_number }}',
                'via' => Message::VIA_EMAIL,
                'sort' => 3,
            ],
            [
                'event' => 'order_created',
                'label' => 'Order Created Admin WA',
                'role' => 'Admin',
                'subject' => 'Order Created',
                'template' => 'Invoice {{ invoice_number }}',
                'via' => Message::VIA_WHATSAPP,
                'sort' => 4,
            ],
        ]);
    }

    private function createSchema(): void
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->integer('order_expired_time')->default(24);
            $table->string('payment_default')->default('Tripay');
            $table->boolean('is_service_fee')->default(false);
            $table->integer('service_fee')->default(0);
            $table->boolean('is_unique_code')->default(false);
            $table->timestamps();
        });

        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('role_id')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('sku');
            $table->integer('price')->default(0);
            $table->integer('stock')->nullable();
            $table->integer('weight')->default(0);
            $table->boolean('status')->default(true);
            $table->string('product_type');
            $table->timestamps();
        });

        Schema::create('product_varians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable();
            $table->foreignId('varian_id')->nullable();
            $table->string('label')->nullable();
            $table->string('value')->nullable();
            $table->string('sku');
            $table->integer('price')->default(0);
            $table->integer('stock')->nullable();
            $table->boolean('has_subvarian')->default(false);
            $table->integer('weight')->default(0);
            $table->timestamps();
        });

        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
        });

        Schema::create('product_promos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->nullable();
            $table->foreignId('product_id')->nullable();
            $table->string('discount_type')->nullable();
            $table->integer('discount_amount')->default(0);
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('voucher_code')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('discount_type');
            $table->integer('discount_amount')->default(0);
            $table->integer('max_discount_amount')->default(0);
            $table->integer('min_transaction')->default(0);
            $table->boolean('is_type_shipping')->default(false);
            $table->integer('usage_quota')->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_ref')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_whatsapp')->nullable();
            $table->integer('order_qty')->default(0);
            $table->integer('order_subtotal')->default(0);
            $table->integer('order_weight')->default(0);
            $table->integer('order_unique_code')->default(0);
            $table->integer('order_total')->default(0);
            $table->string('order_status');
            $table->text('note')->nullable();
            $table->string('shipping_type')->nullable();
            $table->string('shipping_courier_id')->nullable();
            $table->string('shipping_courier_code')->nullable();
            $table->string('shipping_courier_name')->nullable();
            $table->string('shipping_courier_service')->nullable();
            $table->integer('shipping_cost')->nullable();
            $table->text('shipping_address')->nullable();
            $table->timestamp('shipping_delivered')->nullable();
            $table->timestamp('shipping_received')->nullable();
            $table->integer('payment_fee')->default(0);
            $table->integer('service_fee')->default(0);
            $table->integer('voucher_discount')->default(0);
            $table->integer('shipping_discount')->default(0);
            $table->boolean('is_reviewed')->default(false);
            $table->timestamp('expired_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->string('product_type')->default('Digital');
            $table->string('shipping_coordinate')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->string('payment_ref')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_name')->nullable();
            $table->string('payment_code')->nullable();
            $table->integer('amount')->default(0);
            $table->integer('amount_received')->default(0);
            $table->integer('total_fee')->default(0);
            $table->integer('fee_merchant')->default(0);
            $table->integer('fee_customer')->default(0);
            $table->integer('expired_time')->nullable();
            $table->text('instructions')->nullable();
            $table->string('status')->default('UNPAID');
            $table->timestamp('paid_at')->nullable();
            $table->text('note')->nullable();
            $table->string('payment_proof')->nullable();
            $table->string('qr_url')->nullable();
            $table->string('qr_string')->nullable();
            $table->string('snap_token')->nullable();
            $table->string('pay_url')->nullable();
            $table->string('checkout_url')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku');
            $table->foreignId('order_id');
            $table->foreignId('product_id');
            $table->integer('quantity');
            $table->integer('price');
            $table->string('note')->nullable();
            $table->string('image_url')->nullable();
            $table->string('product_url')->nullable();
            $table->foreignId('affiliate_id')->nullable();
            $table->timestamps();
        });

        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->date('date');
            $table->time('time');
            $table->string('description');
            $table->string('city_name')->nullable();
            $table->string('manifest_code')->nullable();
        });

        Schema::create('order_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreignId('voucher_id');
            $table->timestamps();
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('event');
            $table->string('label')->nullable();
            $table->string('role');
            $table->string('subject')->nullable();
            $table->text('template')->nullable();
            $table->string('via')->default(Message::VIA_EMAIL);
            $table->integer('sort')->default(0);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('via');
            $table->string('event')->nullable();
            $table->string('recipient')->nullable();
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('status')->default(Message::Pending);
            $table->text('error_log')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }
}
