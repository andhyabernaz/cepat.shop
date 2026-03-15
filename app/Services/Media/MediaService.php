<?php

namespace App\Services\Media;

use App\Models\Asset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class MediaService
{
   public $width = 800;
   public $extension = 'webp';

   public function getFile($path)
   {
      return Storage::disk('upload')->get($path);
   }
   public function exists($path)
   {
      return Storage::disk('upload')->exists($path);
   }
   public function scale($width)
   {
      $this->width = $width;
      return $this;
   }
   public function toPng()
   {
      $this->extension = 'png';
      return $this;
   }
   public function storeFile($file, $path = 'upload/images')
   {
      $dir = public_path($path);

      if (!File::isDirectory($dir)) {
         File::makeDirectory($dir, 0775, true, true);
      }

      $name = $file->getClientOriginalName();

      $ext = $file->extension();

      if ('svg' == $ext) {
         $filepath = Storage::disk('upload')->put($path, $file);
      } else {

         $filepath = rtrim($path, '/') . '/' . uniqid('img__', true) . '_' . Str::random(8) . '.' . $this->extension;
         $absolutePath = public_path($filepath);

         if ($this->extension == 'png') {
            Image::read($file)->scale($this->width)->toPng()->save($absolutePath);
         } else {
            Image::read($file)->scale($this->width)->toWebp()->save($absolutePath);
         }
      }

      return [
         'filename' => $name,
         'filepath' => $filepath,
         'visibility' => 'public',
         'disk' => 'upload'
      ];
   }
   public function storeDataUrl($file, $path = 'upload/images')
   {
      $dir = public_path($path);

      if (!File::isDirectory($dir)) {
         File::makeDirectory($dir, 0775, true, true);
      }

      $filename =  uniqid('img__', true) . '_' . Str::random(8) . '.webp';

      $filepath = rtrim($path, '/') . '/' . $filename;

      Image::read($file)->toWebp()->save(public_path($filepath));

      return [
         'filename' => $filename,
         'filepath' => $filepath,
         'visibility' => 'public',
         'disk' => 'upload'
      ];
   }
   public function storePrivateFile($file, $path = 'files')
   {

      try {

         $filename = $file->getClientOriginalName();

         $filepath = Storage::disk('local')->put($path, $file);

         $filesize = $file->getSize();
         $filesize = ceil($filesize / 1000);

         return [
            'filename' => $filename,
            'filepath' => $filepath,
            'disk' => 'local',
            'filesize' => $filesize
         ];
      } catch (\Exception $e) {

         throw $e;
      }
   }
   public function deleteAsset($asset)
   {
      Storage::disk($asset->disk)->delete($asset->filepath);
      DB::table('product_asset')->where('asset_id', $asset->id)->delete();
      $asset->delete();

      return true;
   }
   public function deleteAssetByFilename($filename)
   {
      $asset = Asset::where('filename', $filename)->first();

      if ($asset) {

         Storage::disk($asset->disk)->delete($asset->filepath);
         $asset->delete();
      }

      return true;
   }

   public function deleteFile($filepath)
   {
      Storage::disk('upload')->delete($filepath);
   }
}
