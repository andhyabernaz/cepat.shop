<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SseController extends Controller
{
    public function stream($orderId)
    {
        // / Matikan buffer
        @ini_set('output_buffering', 'off');
        @ini_set('zlib.output_compression', false);
        @ini_set('implicit_flush', true);
        while (ob_get_level() > 0) ob_end_flush();

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Access-Control-Allow-Origin: *');

        // Limit 30–45 detik biar tidak makan proses server
        $start = time();
        $timeout = 30;

        while (true) {

            if (connection_aborted()) {
                break;
            }

            // Jika sudah timeout → tutup koneksi, client auto reconnect
            if (time() - $start > $timeout) {
                echo "event: close\n";
                echo "data: timeout\n\n";
                flush();
                break;
            }

            // Ambil status pembayaran dari database
            $status = DB::table('transactions')
                ->where('order_id', $orderId)
                ->value('status');

            echo "data: " . json_encode([
                'status' => $status,
                'time' => now()->toDateTimeString()
            ]) . "\n\n";

            flush();
            // Tunda 3 detik: aman untuk shared hosting
            sleep(3);

            // Jika sudah PAID → langsung tutup stream
            if ($status != Transaction::UNPAID) {
                echo "event: close\n";
                echo "data: {$status}\n\n";
                flush();
                break;
            }
        }
    }
    public function polling($trxId)
    {
        $status = DB::table('transactions')->where('id', $trxId)->value('status');
        return ApiResponse::success($status);
    }

    public function streamPayments($trxId)
    {
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', false);
        ini_set('implicit_flush', true);
        ini_set('no-gzip', '1');

        // SSE Headers
        $headers = [
            'Content-Type'  => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection'    => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ];


        $start = time();
        $timeout = 30;

        return response()->stream(function () use ($start, $timeout, $trxId) {

            // Jangan biarkan PHP auto-timeout
            set_time_limit(0);

            while (true) {

                // Stop loop bila client disconnect
                if (connection_aborted()) {
                    break;
                }

                if (time() - $start > $timeout) {
                    echo "event: close\n";
                    echo "data: timeout\n\n";
                    ob_flush();
                    flush();
                    break;
                }

                $cacheKey = 'payments_updated_' . $trxId;
                $event = Cache::pull($cacheKey);

                if ($event) {
                    echo "event: {$event['event']}\n";
                    echo "data: " . json_encode($event['payload']) . "\n\n";
                    ob_flush();
                    flush();
                }

                echo "event: heartbeat\n";
                echo "data: {}\n\n";
                ob_flush();
                flush();

                sleep(2);
            }
        }, 200, $headers);
    }
}
