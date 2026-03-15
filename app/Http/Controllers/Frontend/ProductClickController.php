<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProductClickController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|integer',
            'product_slug' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer',
            'category_slug' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:100',
            'referrer' => 'nullable|string|max:2048',
        ]);

        $user = auth('sanctum')->user();

        DB::table('product_clicks')->insert([
            'product_id' => $validated['product_id'] ?? null,
            'product_slug' => $validated['product_slug'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'category_slug' => $validated['category_slug'] ?? null,
            'user_id' => $user?->id,
            'session_id' => $request->header('Session-User'),
            'source' => $validated['source'] ?? null,
            'referrer' => $validated['referrer'] ?? null,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return ApiResponse::success();
    }
}
