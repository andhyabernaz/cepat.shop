<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends FormRequest
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
         'is_primary' => ['required'],
         'title' => ['required'],
         'address_street' => ['required'],
         'receiver_name' => ['required'],
         'receiver_phone' => ['required'],
         'receiver_email' => ['nullable'],
         'subdistrict' => ['nullable'],
         'subdistrict_id' => ['nullable'],
         'city' => ['nullable'],
         'city_id' => ['nullable'],
         'province' => ['nullable'],
         'postal_code' => ['nullable'],
         'vendor_id' => ['nullable'],
         'coordinate' => ['required'],
      ];
   }
}
