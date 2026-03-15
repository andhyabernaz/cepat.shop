<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\Channel\EventBuffer;
use Illuminate\Support\Facades\Cache;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-command';

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
         EventBuffer::push(
            channel: 'payments',
            id: 9,
            event: [
                'type' => 'payment.updated',
                'payload' => [
                  'status' => 'UNPAID',
                ],
            ]
        );

        $id = 9;
        $cacheKey = 'payments_updated_' .$id;
        Cache::put($cacheKey, ['event' => 'payment.updated', 'payload' => 'UNPAID'], now()->addMinutes(60));
        

        // Log::debug(EventBuffer::popAll('payments', 9));
    }
}
