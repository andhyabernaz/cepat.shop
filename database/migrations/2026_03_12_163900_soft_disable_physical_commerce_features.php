<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('configs')) {
            $updates = [];

            $booleanColumns = [
                'is_shipping_active',
                'is_order_pickup',
                'is_local_shipping',
                'is_cash_payment',
            ];

            foreach ($booleanColumns as $column) {
                if (Schema::hasColumn('configs', $column)) {
                    $updates[$column] = 0;
                }
            }

            $nullableColumns = [
                'warehouse_id',
                'warehouse_address',
                'rajaongkir_apikey',
                'rajaongkir_couriers',
                'biteship_apikey',
                'biteship_couriers',
                'biteship_warehouse',
                'warehouse_coordinate',
                'local_shipping_costs',
            ];

            foreach ($nullableColumns as $column) {
                if (Schema::hasColumn('configs', $column)) {
                    $updates[$column] = null;
                }
            }

            $defaultValueColumns = [
                'courier_default' => 'Rajaongkir',
                'local_shipping_label' => 'Via Kurir Toko',
            ];

            foreach ($defaultValueColumns as $column => $value) {
                if (Schema::hasColumn('configs', $column)) {
                    $updates[$column] = $value;
                }
            }

            if (!empty($updates)) {
                DB::table('configs')->update($updates);
            }
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'product_type') && Schema::hasColumn('products', 'status')) {
            DB::table('products')
                ->where('product_type', 'Default')
                ->update(['status' => 0]);
        }
    }

    public function down(): void
    {
        // no-op: this migration intentionally archives data without destructive changes
    }
};
