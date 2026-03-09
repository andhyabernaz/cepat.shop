<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
   public function orders(Request $request)
   {
      $status = $request->status;
      $start_date = $request->start_date ?? null;
      $end_date = $request->end_date ?? null;

      $filename = 'orders-' . now()->format('ymdhis') . '_' . $status . '.xlsx';

      return Excel::download(new OrderExport($status, $start_date, $end_date), $filename);
   }
}
