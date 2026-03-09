<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
   public function index()
   {
      $data =  UserAddress::where('user_id', request()->user()->id)
         ->orderByDesc('is_primary')
         ->get();

      return ApiResponse::success($data);
   }
   public function update(UserAddressRequest $request, $id)
   {
      $validated = $request->validated();

      if ($request->boolean('is_primary') == true) {
         UserAddress::where('user_id', $request->user()->id)->update(['is_primary' => false]);
      }

      $userAddress = UserAddress::findOrFail($id);
      $validated['is_primary'] = $request->boolean('is_primary') ? true : false;

      $userAddress->update($validated);

      return ApiResponse::success($userAddress->fresh());
   }
   public function store(UserAddressRequest $request)
   {
      $validated = $request->validated();

      $user = $request->user();

      if ($request->boolean('is_primary') == true) {
         UserAddress::where('user_id', $user->id)->update(['is_primary' => false]);
      } else {
         if (UserAddress::where('user_id', $user->id)->where('is_primary', 1)->count() < 1) {
            $validated['is_primary'] = 1;
         }
      }

      $userAddress = $user->address()->create($validated);

      return ApiResponse::success($userAddress);
   }

   public function destroy($id)
   {
      UserAddress::findOrFail($id)->delete();

      return ApiResponse::success();
   }
}
