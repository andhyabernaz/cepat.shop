<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\BankAccountRequest;

class BankController extends Controller
{

   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $data = BankAccount::all();

      return ApiResponse::success($data);
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(BankAccountRequest $request)
   {
      $validated = $request->validated();

      $data = BankAccount::create($validated);
      Cache::forget('bank_account_counts');

      return ApiResponse::success($data);
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function update(BankAccountRequest $request, $id)
   {
      $validated = $request->validated();

      $bank = BankAccount::findOrFail($id);

      $bank->update($validated);

      Cache::forget('bank_account_counts');

      return ApiResponse::success();
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy($id)
   {
      $bank = BankAccount::findOrFail($id);

      $bank->delete();

      Cache::forget('bank_account_counts');

      return ApiResponse::success();
   }
}
