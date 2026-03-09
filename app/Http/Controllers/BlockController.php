<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class BlockController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $data = Block::with('post:id,slug,title')->orderBy('position', 'desc')->get();

      return ApiResponse::success($data);
   }
   public function show($id)
   {
      $data =  Block::find($id)->load('post');
      return ApiResponse::success($data);
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
         'label' => ['required'],
         'image' => ['required'],
         'position' => ['required'],
      ]);

      $path = public_path('/upload/images');

      if (!File::exists($path)) {
         File::makeDirectory($path, 0755, true, true);
      }

      DB::beginTransaction();

      try {
         $block = new Block();

         $block->label = $request->label;
         $block->description = $request->description;
         $block->position = $request->position;
         $block->weight = $request->weight;
         $block->is_show_title = $request->boolean('is_show_title');

         if ($request->post_id) {
            $block->post_id = $request->post_id;
         }

         if ($file = $request->file('image')) {
            $filename = Str::random(42) . '.' . $file->extension();

            if ($file->move($path, $filename)) {

               $block->image = $filename;
            }
         }

         $block->save();

         DB::commit();

         Cache::forget('blocks');

         return ApiResponse::success($block);
      } catch (\Throwable $th) {
         DB::rollback();

         return ApiResponse::failed($th);
      }
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, $id)
   {
      $request->validate([
         'label' => ['required'],
      ]);

      $path = public_path('/upload/images');

      DB::beginTransaction();

      try {
         $block = Block::findOrFail($request->id);

         $block->label = $request->label;
         $block->description = $request->description;
         $block->position = $request->position;
         $block->weight = $request->weight;
         $block->is_show_title = $request->boolean('is_show_title');

         if ($request->post_id) {
            $block->post_id = $request->post_id;
         } else {
            $block->post_id = null;
         }
         if ($file = $request->file('image')) {
            $filename = Str::random(42) . '.' . $file->extension();

            if ($block->image) {
               File::delete('upload/images/' . $block->image);
            }

            if ($file->move($path, $filename)) {

               $block->image = $filename;
            }
         }

         $block->save();

         DB::commit();

         Cache::forget('blocks');

         return ApiResponse::success($block);
      } catch (\Throwable $th) {

         DB::rollBack();
         return ApiResponse::failed($th);
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

      try {

         $block = Block::find($id);

         if ($block->image) {
            File::delete('upload/images/' . $block->image);
         }
         $block->delete();

         Cache::forget('blocks');
         return ApiResponse::success();
      } catch (\Throwable $th) {
         return ApiResponse::failed($th);
      }
   }

   public function setPostLink(Request $request)
   {
      $request->validate([
         'block_id' => 'required',
         'post_id' => 'nullable'
      ]);

      $block = Block::find($request->block_id);

      $block->update([
         'post_id' => $request->post_id
      ]);

      return ApiResponse::success($block);
   }
}
