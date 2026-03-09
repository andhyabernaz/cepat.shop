<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Transaction;
use App\Events\OrderExpired;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CheckExpierationOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-order-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (Cache::has('is_stock_reset')) {
            return 0;
        }
        $orderExpireds = Order::whereNotNull('expired_at')
            ->where('expired_at', '<', Carbon::now())
            ->where(function ($q) {
                $q->where('order_status', 'UNPAID')
                    ->orWhere('order_status', 'PENDING');
            })
            ->get();

        Cache::put('is_stock_reset', 1, now()->addMinutes(2));

        if (count($orderExpireds) > 0) {
            foreach ($orderExpireds as $order) {
                $msg = 'Pembayaran kadaluarsa';
                OrderExpired::dispatch($order, $msg);
            }
        }
    }
}
