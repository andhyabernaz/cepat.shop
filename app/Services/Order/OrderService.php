<?php

namespace App\Services\Order;

use App\Enums\MutasiSaldoStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Jobs\ProcessAffiliateOrderJob;
use App\Models\License;
use App\Models\MutasiSaldo;
use App\Models\Order;
use App\Models\Product;

class OrderService
{
    public function processOrder($order)
    {
        $transaction = $order->transaction;
        if (! $transaction) {
            throw new \Exception("Transaction not found for order #{$order->id}");
        }
        $order_status = Order::TOSHIP;

        $is_completion_order = false;

        if ($order->is_digital_type()) {

            $order_status = Order::TO_PROCESS;

            if (in_array($order->product_type, [ProductTypeEnum::DigitalDownload->value, ProductTypeEnum::DigitalVideo->value])) {
                $is_completion_order = true;
                foreach ($order->items as $item) {

                    $expiredDate = null;

                    if ($item->license_id) {
                        $license = License::find($item->license_id);
                        if ($license) {
                            $license->update([
                                'expired_at' => $expiredDate,
                            ]);
                        }
                    } else {
                        if ($order->user_id) {
                            $order_status = Order::COMPLETE;
                            $license = License::create([
                                'user_id' => $order->user_id,
                                'product_id' => $item->product_id,
                                'expired_at' => $expiredDate,
                            ]);
                        } else {
                            $order_status = Order::TO_PROCESS;
                        }
                    }
                }
            } else {
                $order_status = Order::TO_PROCESS;
            }
        } elseif ($order->is_deposit_type()) {

            MutasiSaldo::create([
                'amount' => $order->order_total,
                'type' => MutasiSaldo::TYPE_IN,
                'category' => MutasiSaldo::CATEGORY_DEFAULT,
                'user_id' => $order->user_id,
                'status' => MutasiSaldoStatusEnum::Success,
                'description' => $order->item->name.' #'.$order->order_ref,
            ]);

            $order_status = Order::COMPLETE;
        } else {
            if ($order->shipping_type == Order::SHIPPING_PICKUP) {
                $order_status = Order::AWAITING_PICKUP;
            }
        }

        if (! $order->is_deposit_type()) {

            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('sold', $item->quantity);
                }
            }
        }
        $order->order_status = $order_status;
        $order->updated_at = now();
        $order->save();
        $transaction->status = 'PAID';
        $transaction->paid_at = now();
        $transaction->save();

        if ($is_completion_order) {
            $this->completionOrder($order);
        }
    }

    public function completionOrder($order)
    {

        $order->update([
            'order_status' => Order::COMPLETE,
        ]);

        $order->transaction()->update([
            'status' => 'PAID',
        ]);

        if (! $order->is_deposit_type()) {

            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('sold', $item->quantity);
                }
            }
        }

        $order->pushHistory('Pesanan selesai');

        ProcessAffiliateOrderJob::dispatch($order);
    }
}
