<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentServiceController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}
    public function paymentChanels(Request $request)
    {
        try {
            $params = [
                'amount' => $request->amount
            ];

            $data = $this->paymentService->paymentChanels($params);
            return ApiResponse::success($data);
        } catch (\Throwable $th) {
            return ApiResponse::failed($th->getMessage());
        }
    }
}
