<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Console\Command;

class UpdateOrderStatus extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'order:change-status';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Command description';

   /**
    * Create a new command instance.
    *
    * @return void
    */
   public function __construct()
   {
      parent::__construct();
   }

   /**
    * Execute the console command.
    *
    * @return int
    */
   public function handle()
   {
      Order::where('order_status', 'PAID')
         ->orWhere('order_status', 'PROCESS')
         ->update(['order_status' => 'TOSHIP']);

      Order::where('order_status', 'UNPAID')
         ->whereHas('transaction', function ($q) {
            $q->where('payment_type', 'COD');
         })
         ->update(['order_status' => 'TOSHIP']);

      Order::where('order_status', 'UNPAID')
         ->update(['order_status' => 'PENDING']);

      Transaction::where('payment_type', 'BANK_TRANSFER')->update([
         'payment_type' => Order::PAYMEMT_DIRECT_TRANSFER
      ]);
   }
}
