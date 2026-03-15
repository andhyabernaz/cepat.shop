<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use App\Services\Media\MediaService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
   public function __construct(
      protected MediaService $mediaService
   ) {}
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index(Request $request)
   {
      $query = $request->query('q');
      $posts = Post::when($query, function ($q) use ($query) {
         if ($query == 'listing') {
            $q->listing();
         }
         if ($query == 'promote') {
            $q->promote();
         }
      })->with('asset')->latest()->paginate($request->per_page ?? 5)->withQueryString();

      return ApiResponse::success($posts);
   }
   public function getListing()
   {
      $data = Post::listing()->latest()->get();
      return ApiResponse::success($data);
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(PostRequest $request)
   {
      $post = new Post();

      $post->title = $request->title;
      $post->slug = Str::slug($request->title);
      $post->tags = $request->tags ?? NULL;
      $post->body = $request->body;
      $post->category = $request->category ?? NULL;
      $post->user_id = $request->user()->id;

      $post->is_listing = $request->boolean('is_listing');
      $post->is_promote = $request->boolean('is_promote');

      $post->save();

      if ($file = $request->file('image')) {

         $filedata = $this->mediaService->storeFile($file);
         $post->asset()->create($filedata);
      }

      $this->cacheClear();

      return ApiResponse::success($post->load('asset'));
   }

   /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function show($id)
   {
      $data = Post::with('asset')->findOrFail($id);
      return ApiResponse::success($data);
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
         'title' => ['required'],
         'image' => $request->boolean('del_image') ? ['required'] : ['nullable'],
         'body' => ['required']
      ]);

      $post = Post::findOrFail($id);

      if ($file = $request->file('image')) {

         if ($post->asset) {
            $this->mediaService->deleteAsset($post->asset);
         }

         $filedata = $this->mediaService->storeFile($file);
         $post->asset()->create($filedata);
      }

      $post->title = $request->title;
      $post->tags = $request->tags ?? NULL;
      $post->body = $request->body;
      $post->category = $request->category ?? NULL;

      $post->is_listing = $request->boolean('is_listing');
      $post->is_promote = $request->boolean('is_promote');

      $post->save();

      $this->cacheClear();
      return ApiResponse::success($post);
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy($id)
   {
      $post = Post::findOrFail($id);

      if ($post->asset) {
         $this->mediaService->deleteAsset($post->asset);
      }

      $post->delete();

      $this->cacheClear();

      return ApiResponse::success();
   }

   public function postTags()
   {
      $tags = Post::select('tags')->whereNotNull('tags')->groupBy('tags')->get()->map(function ($tag) {
         return $tag->tags;
      });
      return ApiResponse::success($tags);
   }


   protected function cacheClear()
   {
      Cache::flush();
   }
}
