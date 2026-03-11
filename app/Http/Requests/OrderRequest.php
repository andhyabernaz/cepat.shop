<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
   public function authorize()
   {
      return true;
   }

   public function rules()
   {
      $rule = [
         'product_type' => ['required'],
         'customer_name' => ['required', 'string'],
         'customer_phone' => ['required', 'string'],
         'customer_email' => ['required', 'email'],
         'payment_type' =>  ['required', 'string'],
         'payment_method' =>  ['required', 'string'],
         'payment_name' =>  ['required', 'string'],
         'payment_code' => ['nullable'],
         'order_items' => ['required', 'array'],
         'order_qty' => ['required', 'numeric'],
         'order_unique_code' => ['required', 'numeric'],
         'order_subtotal' => ['required', 'numeric'],
         'order_total' => ['required', 'numeric'],
         'grand_total' => ['required', 'numeric'],
         'payment_fee' => ['required', 'numeric'],
         'service_fee' => ['required', 'numeric'],
         'voucher_discount' => ['required', 'numeric'],
      ];

      return $rule;
   }
}
