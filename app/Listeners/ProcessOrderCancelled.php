<?php

namespace App\Listeners;

use App\Models\Order;
use App\Models\MutasiSaldo;
use App\Models\Transaction;
use App\Events\OrderCancelled;
use App\Enums\MutasiSaldoStatusEnum;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessOrderCancelled
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCancelled $event): void
    {
        $order = $event->order;

        $order->update([
            'order_status' => Order::CANCELED,
            'cancellation_reason' => $event->reason
        ]);

        $transaction = $order->transaction;
        $trx_status = $transaction->status;

        if ($transaction->status == Transaction::PAID) {

            MutasiSaldo::create([
                'amount' => $order->order_total,
                'type' => MutasiSaldo::TYPE_IN,
                'category' => MutasiSaldo::CATEGORY_DEFAULT,
                'user_id' => $order->user_id,
                'status' => MutasiSaldoStatusEnum::Success,
                'description' => 'Pengembalian dana transaksi #' . $order->order_ref,
            ]);

            $trx_status = Transaction::REFUND;
        }

        $transaction->update([
            'status' => $trx_status
        ]);

        $msg = "Pesanan dibatalkan, {$event->reason}";
        $order->pushHistory($msg);
        $order->flush_cache();
    }
}
