<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{

   public function index()
   {
      $with = request()->query('with');
      $is_parent = request()->query('is_parent') ?? false;
      $data = Category::when($with, function($q) use ($with) {
         if ($with == 'parent') {
            $q->withParent();
         } elseif ($with == 'childs') {
            $q->withChilds();
         } 
      })->when($is_parent, function($q) {
         $q->onlyParents();
      })->get();


      return ApiResponse::success($data);
   }
   public function store(Request $request)
   {
      $request->validate([
         'title' => 'required|unique:categories',
         'images' => $request->category_id ? 'nullable' : 'required'
      ]);

      DB::beginTransaction();

      try {
         $path = public_path('/upload/images');

         if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
         }

         $category = new Category();
         $category->title = $request->title;
         $category->category_id = $request->category_id ?? NULL;
         $category->is_front = $request->boolean('is_front');
         $category->is_background_banner = $request->boolean('is_background_banner');
         $category->weight = $request->weight;
         $category->created_at = now();
         $category->updated_at = now();

         $category->description = $request->description;

         if ($file = $request->file('images')) {

            $imageName = Str::random(40) . '.' . $file->extension();

            $file->move($path, $imageName);

            $category->filename = $imageName;
         }
         if ($file = $request->file('banner')) {

            $imageName = Str::random(41) . '.' . $file->extension();

            $file->move($path, $imageName);

            $category->banner = $imageName;
         }

         $category->save();

         DB::commit();

         Cache::forget('categories');
         Product::clearCache();

         return ApiResponse::success($category);
      } catch (Exception $e) {

         DB::rollBack();

         return ApiResponse::failed($e->getMessage());
      }
   }
   public function show($id)
   {
      $data = Category::find($id);
      return ApiResponse::success($data);
   }

   public function update(Request $request, $id)
   {
      $request->validate([
         'title' => 'required',
         'weight' => 'required',
      ]);


      DB::beginTransaction();

      try {
         $category = Category::find($id);
         $category->title = $request->title;
         $category->category_id = $request->category_id ?? NULL;
         $category->is_front = $request->boolean('is_front');
         $category->is_background_banner = $request->boolean('is_background_banner');
         $category->weight = $request->weight;
         $category->description = $request->description;
         $category->updated_at = now();

         $category->save();

         if ($file = $request->file('images')) {

            $oldAssets = $category->filename;

            $imageName = Str::random(39) . '.' . $file->extension();

            $file->move(public_path('/upload/images'), $imageName);

            File::delete('upload/images/' . $oldAssets);

            $category->filename = $imageName;
         }
         if ($request->boolean('remove_banner')) {

            File::delete('upload/images/' .  $category->banner);
            $category->banner = '';
         }
         if ($fileBanner = $request->file('banner')) {


            if ($category->banner) {
               File::delete('upload/images/' . $category->banner);
            }

            $bannerName = Str::random(41) . '.' . $fileBanner->extension();

            $fileBanner->move(public_path('/upload/images'), $bannerName);


            $category->banner = $bannerName;
         }

         $category->save();

         if ($request->category_id) {
            $category->childs()->delete();
         }

         DB::commit();

         Cache::forget('categories');
         Product::clearCache();

         return ApiResponse::success($category);
      } catch (Exception $e) {
         DB::rollBack();

         return ApiResponse::failed($e->getMessage());
      }
   }

   public function destroy($id)
   {
      $category = Category::find($id);

      $category->childs()->delete();

      File::delete('upload/images/' . $category->filename);
      File::delete('upload/images/' . $category->banner);

      $category->delete();

      Cache::forget('categories');
      Product::clearCache();

      return ApiResponse::success();
   }
}
