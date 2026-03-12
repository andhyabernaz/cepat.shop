<?php

namespace App\Http\Requests;

use App\Enums\ProductTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
   public function authorize()
   {
      return true;
   }

   public function rules()
   {
      $rules = [
         'title' => 'required|string|max:190',
         'product_type' => ['required', Rule::in(ProductTypeEnum::getNonPhysicalValues())],
         'price' => 'required',
         'description' => 'required',
         'aff_amount' => 'numeric',
         'assets' => ['required', 'array'],
      ];

      if ($this->product_type == ProductTypeEnum::DigitalVideo->value) {
         $rules['digital_videos'] = ['required', 'array'];
      }
      if ($this->product_type == ProductTypeEnum::DigitalDownload->value) {
         $rules['digital_downloads'] = ['required', 'array'];
      }

      return $rules;
   }

   public function messages()
   {
      return [
         'title.unique' => 'Nama produk sudah digunakan'
      ];
   }

   protected function prepareForValidation(): void
   {
      $this->merge([
         'title' => strip_tags($this->title),
      ]);
   }
}
