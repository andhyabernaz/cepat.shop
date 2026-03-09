<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerReviewRequest extends FormRequest
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
         'order_id'              => ['required'],
         'items'                 => ['required', 'array'],
         'items.*.product_id'    => ['required'],
         'items.*.comment'       => ['required', 'string', 'max:300'],
         'items.*.product_name'  => ['required'],
         'items.*.rating'        => ['required', 'numeric', 'min:1', 'max:5'],
      ];
   }

   public function messages()
   {
      return [
         'items.*.rating.min' => 'Rating item #:nth tidak boleh kosong',
         'items.*.rating.max' => 'Rating item #:nth maksimal 5 bintang',
         'items.*.rating.required' => 'Rating #:nth tidak boleh kosong',
         'items.*.rating.numeric' => 'Rating #:nth must be between :min and :max.',
         'items.*.comment.required' => 'Ulasan #:nth tidak boleh kosong',
      ];
   }
}
