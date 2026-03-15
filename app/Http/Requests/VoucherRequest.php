<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
{
   /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
   public function authorize()
   {
      return true;
   }

   /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
   public function rules()
   {

      return [
         'name' => ['required'],
         'voucher_code' => ['required'],
         'start_date' => ['required'],
         'end_date' => ['required'],
         'discount_type' => ['required'],
         'discount_amount' => ['required', 'numeric'],
         'max_discount_amount' => ['required', 'numeric'],
         'min_transaction' => ['required', 'numeric'],
         'usage_quota' => ['required', 'numeric'],
         'is_type_shipping' => ['required'],
      ];
   }
}
