<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateProductSold extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-product-sold';

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
        $orderItems = OrderItem::all();

        foreach ($orderItems as $item) {
            DB::table('products')->where('id', $item->product_id)->increment('sold', $item->quantity);
        }
    }
}
