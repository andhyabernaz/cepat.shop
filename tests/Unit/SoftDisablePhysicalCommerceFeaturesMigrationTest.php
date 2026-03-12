<?php

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SoftDisablePhysicalCommerceFeaturesMigrationTest extends TestCase
{
    private string $previousConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousConnection = config('database.default');

        config([
            'database.connections.migration_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'database.default' => 'migration_test',
        ]);

        DB::purge('migration_test');
        DB::reconnect('migration_test');
    }

    protected function tearDown(): void
    {
        DB::disconnect('migration_test');
        config(['database.default' => $this->previousConnection]);

        parent::tearDown();
    }

    public function test_migration_resets_physical_commerce_config_without_null_constraint_violations(): void
    {
        $this->createConfigTable();
        $this->createProductsTable();

        DB::table('configs')->insert([
            'id' => 1,
            'is_shipping_active' => 1,
            'is_order_pickup' => 1,
            'is_local_shipping' => 1,
            'is_cash_payment' => 1,
            'courier_default' => 'Biteship',
            'warehouse_id' => 10,
            'warehouse_address' => 'Gudang A',
            'rajaongkir_apikey' => 'abc123',
            'rajaongkir_couriers' => 'jne:sicepat',
            'biteship_apikey' => 'secret',
            'biteship_couriers' => 'gojek',
            'biteship_warehouse' => 'central',
            'warehouse_coordinate' => '-6.2,106.8',
            'local_shipping_costs' => '{"area":"10000"}',
            'local_shipping_label' => 'Kurir Internal',
        ]);

        DB::table('products')->insert([
            ['id' => 1, 'product_type' => 'Default', 'status' => 1],
            ['id' => 2, 'product_type' => 'Digital', 'status' => 1],
        ]);

        $migration = require base_path('database/migrations/2026_03_12_163900_soft_disable_physical_commerce_features.php');
        $migration->up();

        $config = DB::table('configs')->first();

        $this->assertSame(0, (int) $config->is_shipping_active);
        $this->assertSame(0, (int) $config->is_order_pickup);
        $this->assertSame(0, (int) $config->is_local_shipping);
        $this->assertSame(0, (int) $config->is_cash_payment);
        $this->assertSame('Rajaongkir', $config->courier_default);
        $this->assertNull($config->warehouse_id);
        $this->assertNull($config->warehouse_address);
        $this->assertNull($config->rajaongkir_apikey);
        $this->assertNull($config->rajaongkir_couriers);
        $this->assertNull($config->biteship_apikey);
        $this->assertNull($config->biteship_couriers);
        $this->assertNull($config->biteship_warehouse);
        $this->assertNull($config->warehouse_coordinate);
        $this->assertNull($config->local_shipping_costs);
        $this->assertSame('Via Kurir Toko', $config->local_shipping_label);

        $this->assertSame(0, (int) DB::table('products')->where('id', 1)->value('status'));
        $this->assertSame(1, (int) DB::table('products')->where('id', 2)->value('status'));
    }

    private function createConfigTable(): void
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_shipping_active')->default(1);
            $table->boolean('is_order_pickup')->default(1);
            $table->boolean('is_local_shipping')->default(1);
            $table->boolean('is_cash_payment')->default(1);
            $table->string('courier_default')->default('Rajaongkir');
            $table->integer('warehouse_id')->nullable();
            $table->text('warehouse_address')->nullable();
            $table->string('rajaongkir_apikey')->nullable()->default('apikey-default');
            $table->text('rajaongkir_couriers')->nullable();
            $table->text('biteship_apikey')->nullable();
            $table->text('biteship_couriers')->nullable();
            $table->text('biteship_warehouse')->nullable();
            $table->string('warehouse_coordinate')->nullable();
            $table->text('local_shipping_costs')->nullable();
            $table->string('local_shipping_label')->default('Via Kurir Toko');
        });
    }

    private function createProductsTable(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_type');
            $table->boolean('status')->default(1);
        });
    }
}
