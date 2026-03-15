<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Media\MediaService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class SliderController extends Controller
{

   public function __construct(
      protected MediaService $mediaService
   ) {}
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $data = Slider::OrderBy('weight', 'asc')->with('post:id,slug,title')->get();
      return response([
         'success' => true,
         'data' => $data
      ], 200);
   }
   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
   {

      $request->validate([
         'image' => ['required']
      ]);
      DB::beginTransaction();

      try {
         $path = public_path('/upload/images');

         if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
         }
         $file = $request->file('image');

         $filedata = $this->mediaService->scale(1000)->storeFile($file);

         $last = Slider::OrderBy('weight', 'desc')->first();

         Slider::create([
            'filepath' => $filedata['filepath'],
            'filename' => $filedata['filename'],
            'weight' => $last ? $last->weight + 1 : 1
         ]);

         DB::commit();

         Cache::forget('sliders');

         return response([
            'success' => true,
            'message' => 'Berhasil menambah item',
            'data' => null
         ], 200);
      } catch (\Throwable $th) {

         DB::rollBack();

         return response([
            'success' => true,
            'message' => 'Gagal menambah item, silahkan ulangi lagi',
            'data' => null
         ], 500);
      }
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

   public function destroy($id)
   {
      $slider = Slider::find($id);

      File::delete('upload/images/' . $slider->filename);

      $slider->delete();

      Cache::forget('sliders');

      return response([
         'success' => true,
         'message' => 'Berhasil menghapus item',
      ], 200);
   }
   /**
    * Update slider weight field.
    *
    * @return \Illuminate\Http\Response
    */
   public function updateWeight(Request $request)
   {
      $slider = Slider::find($request->id);
      $slider->weight = (int)$request->value;
      $slider->save();

      Cache::forget('sliders');

      return response([
         'success' => true,
         'message' => 'Berhasil memperbarui item',
      ], 200);
   }

   public function setPostLink(Request $request)
   {
      $request->validate([
         'slider_id' => 'required',
         'post_id' => 'nullable'
      ]);

      $slider = Slider::find($request->slider_id);

      $slider->update([
         'post_id' => $request->post_id
      ]);

      Cache::forget('sliders');

      return ApiResponse::success($slider);
   }
}
