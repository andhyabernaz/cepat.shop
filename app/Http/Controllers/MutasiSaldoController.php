<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\MutasiSaldo;
use Illuminate\Http\Request;

class MutasiSaldoController extends Controller
{
   public function index(Request $request)
   {
      $category = $request->model ?? MutasiSaldo::CATEGORY_DEFAULT;
      $data = MutasiSaldo::with('user')
         ->when($request->user_id, function ($q) use ($request) {
            $q->where('user_id', $request->user_id);
         })
         ->where('category', $category)
         ->latest()->paginate($request->per_page ?? 10)->withQueryString();

      return ApiResponse::success($data);
   }
}
