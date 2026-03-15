<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminDashboardLandingDataTest extends TestCase
{
    private string $previousConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousConnection = config('database.default');

        config([
            'installer.installed' => true,
            'database.connections.dashboard_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'database.default' => 'dashboard_test',
        ]);

        DB::purge('dashboard_test');
        DB::reconnect('dashboard_test');
        Cache::flush();

        $this->createSchema();
        $this->seedData();
    }

    protected function tearDown(): void
    {
        Cache::flush();
        DB::disconnect('dashboard_test');
        config(['database.default' => $this->previousConnection]);

        parent::tearDown();
    }

    public function test_admin_reports_returns_landing_stats_and_testimonials(): void
    {
        $admin = User::query()->findOrFail(1);
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/adminReports?period=monthly');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(4, 'data.landing_stats')
            ->assertJsonCount(1, 'data.testimonials')
            ->assertJsonPath('data.testimonials.0.name', 'Rina')
            ->assertJsonPath('data.testimonials.0.product_name', 'Landing Kit')
            ->assertJsonPath('data.transaction_reports.0.total', 102000)
            ->assertJsonPath('data.order_reports.0.total', 2)
            ->assertJsonPath('data.landing_stats.0.value', 1)
            ->assertJsonPath('data.landing_stats.1.value', 2)
            ->assertJsonPath('data.landing_stats.2.value', 1)
            ->assertJsonPath('data.landing_stats.3.value', 1);
    }

    public function test_admin_reports_weekly_returns_dashboard_widgets(): void
    {
        $admin = User::query()->findOrFail(1);
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/adminReports?period=weekly&mode=widgets');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.today_orders_total', 0)
            ->assertJsonPath('data.weekly_sales_total', 102000)
            ->assertJsonCount(7, 'data.weekly_sales_labels')
            ->assertJsonCount(7, 'data.weekly_sales_series');
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('phone')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->longText('description')->nullable();
            $table->integer('stock')->nullable();
            $table->bigInteger('price')->default(0);
            $table->integer('sold')->default(0);
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->integer('weight')->default(0);
            $table->string('sku')->nullable();
            $table->string('product_type')->default('Default');
            $table->boolean('aff_is_active')->default(false);
            $table->boolean('aff_is_percentage')->default(false);
            $table->integer('aff_amount')->default(0);
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('name')->nullable();
            $table->text('comment')->nullable();
            $table->tinyInteger('rating')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('product_name');
            $table->string('product_varian')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_ref')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
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
            $table->integer('payment_fee')->default(0);
            $table->integer('service_fee')->default(0);
            $table->integer('voucher_discount')->default(0);
            $table->integer('shipping_discount')->default(0);
            $table->boolean('is_reviewed')->default(false);
            $table->timestamp('expired_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->string('product_type')->default('Default');
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('payment_type')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_name')->nullable();
            $table->string('payment_proof')->nullable();
            $table->integer('fee_merchant')->default(0);
            $table->string('status')->default('UNPAID');
            $table->timestamps();
        });
    }

    private function seedData(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@example.test',
                'role_id' => 1,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'id' => 2,
                'name' => 'Customer',
                'email' => 'customer@example.test',
                'role_id' => null,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
        ]);

        DB::table('products')->insert([
            [
                'id' => 10,
                'title' => 'Landing Kit',
                'slug' => 'landing-kit',
                'description' => 'Paket halaman penjualan',
                'stock' => 10,
                'price' => 100000,
                'status' => 1,
                'weight' => 300,
                'sku' => 'SKU-LANDING',
                'product_type' => 'Default',
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'id' => 11,
                'title' => 'FAQ Builder',
                'slug' => 'faq-builder',
                'description' => 'Komponen FAQ',
                'stock' => 8,
                'price' => 70000,
                'status' => 1,
                'weight' => 200,
                'sku' => 'SKU-FAQ',
                'product_type' => 'Default',
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
        ]);

        DB::table('reviews')->insert([
            [
                'id' => 90,
                'product_id' => 10,
                'name' => 'Rina',
                'comment' => 'Page builder ini bikin section promosi jadi jauh lebih cepat dipasang.',
                'rating' => 5,
                'is_approved' => 1,
                'product_name' => 'Landing Kit',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'id' => 91,
                'product_id' => 11,
                'name' => 'Bimo',
                'comment' => 'Draft ini belum tayang.',
                'rating' => 4,
                'is_approved' => 0,
                'product_name' => 'FAQ Builder',
                'created_at' => now()->subHours(10),
                'updated_at' => now()->subHours(10),
            ],
        ]);

        DB::table('orders')->insert([
            [
                'id' => 20,
                'order_ref' => 'INV260300001AA',
                'user_id' => 2,
                'customer_name' => 'Customer',
                'customer_email' => 'customer@example.test',
                'customer_whatsapp' => '081234567890',
                'order_qty' => 1,
                'order_subtotal' => 100000,
                'order_weight' => 300,
                'order_unique_code' => 0,
                'order_total' => 100000,
                'order_status' => 'COMPLETE',
                'payment_fee' => 2000,
                'expired_at' => now()->addDay(),
                'product_type' => 'Default',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'id' => 21,
                'order_ref' => 'INV260300002BB',
                'user_id' => 2,
                'customer_name' => 'Customer',
                'customer_email' => 'customer@example.test',
                'customer_whatsapp' => '081234567890',
                'order_qty' => 1,
                'order_subtotal' => 70000,
                'order_weight' => 200,
                'order_unique_code' => 0,
                'order_total' => 70000,
                'order_status' => 'PENDING',
                'payment_fee' => 0,
                'expired_at' => now()->addHours(12),
                'product_type' => 'Default',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
        ]);

        DB::table('transactions')->insert([
            [
                'order_id' => 20,
                'status' => 'PAID',
                'fee_merchant' => 3500,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'order_id' => 21,
                'status' => 'UNPAID',
                'fee_merchant' => 0,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
        ]);
    }
}
