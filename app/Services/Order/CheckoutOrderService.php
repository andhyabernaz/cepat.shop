<?php

namespace App\Services\Order;

use App\Enums\ProductTypeEnum;
use App\Models\Config;
use App\Models\Product;
use App\Models\ProductVarian;
use App\Models\Voucher;
use Exception;

class CheckoutOrderService
{
    public function prepare(array $payload): array
    {
        $items = $this->resolveItems($payload['order_items'] ?? []);

        if (! count($items)) {
            throw new Exception('Produk checkout tidak ditemukan.');
        }

        $productType = $items[0]['product_type'];

        if (($payload['product_type'] ?? null) && $payload['product_type'] !== $productType) {
            throw new Exception('Tipe produk checkout tidak valid.');
        }

        $config = Config::query()->first();
        $shipping = $this->resolveShipping($payload);
        $serviceFee = $this->resolveServiceFee($config);
        $paymentFee = max(0, intval($payload['payment_fee'] ?? 0));
        $uniqueCode = $this->resolveUniqueCode($config, $payload);

        $subtotal = 0;
        $weight = 0;
        $qty = 0;

        foreach ($items as $item) {
            $subtotal += intval($item['price']) * intval($item['quantity']);
            $weight += intval($item['weight'] ?? 0) * intval($item['quantity']);
            $qty += intval($item['quantity']);
        }

        [$voucher, $voucherDiscount, $shippingDiscount] = $this->resolveVoucher(
            intval($payload['voucher_id'] ?? 0),
            $subtotal,
            $shipping['cost']
        );

        $total = $subtotal + $shipping['cost'] + $serviceFee + $uniqueCode;
        $grandTotal = max(0, $total - $voucherDiscount - $shippingDiscount);

        return [
            'product_type' => $productType,
            'items' => $items,
            'subtotal' => $subtotal,
            'weight' => $weight,
            'qty' => $qty,
            'service_fee' => $serviceFee,
            'payment_fee' => $paymentFee,
            'unique_code' => $uniqueCode,
            'shipping' => $shipping,
            'voucher' => $voucher,
            'voucher_discount' => $voucherDiscount,
            'shipping_discount' => $shippingDiscount,
            'total' => $total,
            'grand_total' => $grandTotal,
            'billing_total' => $grandTotal + $paymentFee,
        ];
    }

    protected function resolveItems(array $requestedItems): array
    {
        $items = [];

        foreach ($requestedItems as $requestedItem) {
            $productId = intval($requestedItem['product_id'] ?? 0);
            $sku = $requestedItem['sku'] ?? null;
            $quantity = intval($requestedItem['quantity'] ?? 0);

            if ($productId < 1 || ! $sku || $quantity < 1) {
                throw new Exception('Data item checkout tidak valid.');
            }

            $product = Product::with([
                'productPromo' => function ($query) {
                    $query->whereHas('promoActive');
                },
            ])
                ->where('id', $productId)
                ->where('status', 1)
                ->first();

            if (! $product) {
                throw new Exception('Produk checkout tidak ditemukan.');
            }

            if (! in_array($product->product_type, ProductTypeEnum::getNonPhysicalValues(), true)) {
                throw new Exception('Checkout produk fisik sudah dinonaktifkan.');
            }

            $variant = null;

            if ($sku !== $product->sku) {
                $variant = ProductVarian::query()
                    ->where('product_id', $product->id)
                    ->where('sku', $sku)
                    ->first();

                if (! $variant) {
                    throw new Exception("Varian produk {$product->title} tidak ditemukan.");
                }
            }

            $stock = $variant?->stock ?? $product->stock;

            if (! $this->isUnlimitedStock($product, $stock) && $quantity > intval($stock)) {
                throw new Exception("Stok produk {$product->title} tidak mencukupi.");
            }

            $basePrice = intval($variant?->price ?? $product->price);
            $price = $this->applyPromoDiscount($product, $basePrice);

            $items[] = [
                'sku' => $variant?->sku ?? $product->sku,
                'name' => $product->title,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'note' => $requestedItem['note'] ?? null,
                'image_url' => $requestedItem['image_url'] ?? null,
                'product_url' => $requestedItem['product_url'] ?? null,
                'affiliate_code' => $requestedItem['affiliate_code'] ?? null,
                'weight' => intval($variant?->weight ?? $product->weight ?? 0),
                'product_type' => $product->product_type,
            ];
        }

        $productTypes = array_values(array_unique(array_map(fn ($item) => $item['product_type'], $items)));

        if (count($productTypes) > 1) {
            throw new Exception('Checkout langsung hanya mendukung produk dengan tipe yang sama.');
        }

        return $items;
    }

    protected function resolveShipping(array $payload): array
    {
        return [
            'type' => $payload['shipping_type'] ?? null,
            'id' => $payload['shipping_courier_id'] ?? null,
            'code' => $payload['shipping_courier_id'] ?? null,
            'name' => $payload['shipping_courier_name'] ?? null,
            'service' => $payload['shipping_courier_service'] ?? null,
            'cost' => max(0, intval($payload['shipping_cost'] ?? 0)),
            'address' => $payload['shipping_address'] ?? null,
            'coordinate' => $payload['shipping_coordinate'] ?? null,
        ];
    }

    protected function resolveVoucher(int $voucherId, int $subtotal, int $shippingCost): array
    {
        if ($voucherId < 1) {
            return [null, 0, 0];
        }

        $voucher = Voucher::withCount('orders')->where('id', $voucherId)->first();

        if (! $voucher) {
            throw new Exception('Voucher tidak ditemukan.');
        }

        if ($voucher->usage_quota > 0 && $voucher->usage_quota <= $voucher->orders_count) {
            throw new Exception('Kuota pemakaian voucher habis, silahkan ganti voucher yang lain');
        }

        if ($voucher->end_date < now()) {
            throw new Exception('Masa aktif voucher telah kadaluarsa, silahkan ganti voucher yang lain');
        }

        if ($voucher->min_transaction > 0 && $subtotal < intval($voucher->min_transaction)) {
            throw new Exception('Minimal transaksi untuk voucher ini belum terpenuhi.');
        }

        $baseAmount = $voucher->is_type_shipping ? $shippingCost : $subtotal;
        $discount = 0;

        if ($voucher->discount_type == 'percent') {
            $discount = (int) floor(($baseAmount * intval($voucher->discount_amount)) / 100);
        } else {
            $discount = intval($voucher->discount_amount);
        }

        if ($voucher->max_discount_amount > 0 && $discount > intval($voucher->max_discount_amount)) {
            $discount = intval($voucher->max_discount_amount);
        }

        if ($discount > $baseAmount) {
            $discount = $baseAmount;
        }

        return $voucher->is_type_shipping
            ? [$voucher, 0, $discount]
            : [$voucher, $discount, 0];
    }

    protected function resolveServiceFee(?Config $config): int
    {
        if (! $config || ! $config->is_service_fee) {
            return 0;
        }

        return max(0, intval($config->service_fee ?? 0));
    }

    protected function resolveUniqueCode(?Config $config, array $payload): int
    {
        if (! $config || ! $config->is_unique_code) {
            return 0;
        }

        if (($payload['payment_type'] ?? null) !== 'DIRECT_TRANSFER') {
            return 0;
        }

        return max(0, intval($payload['order_unique_code'] ?? 0));
    }

    protected function applyPromoDiscount(Product $product, int $basePrice): int
    {
        $discount = 0;

        if ($product->productPromo) {
            if ($product->productPromo->discount_type === 'PERCENT') {
                $discount = (int) floor(($basePrice * intval($product->productPromo->discount_amount)) / 100);
            } else {
                $discount = intval($product->productPromo->discount_amount);
            }
        }

        $finalPrice = $basePrice - $discount;

        if ($finalPrice < 0) {
            return 0;
        }

        return $finalPrice;
    }

    protected function isUnlimitedStock(Product $product, $stock): bool
    {
        if (in_array($product->product_type, [
            ProductTypeEnum::DigitalDownload->value,
            ProductTypeEnum::DigitalVideo->value,
        ], true)) {
            return true;
        }

        if (is_null($stock)) {
            return true;
        }

        return intval($stock) < 0;
    }
}
