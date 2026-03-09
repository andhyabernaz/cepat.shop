<?php

namespace App\Http\Controllers;

use App\Models\UploadTemp;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\DigitalDownload;
use App\Services\Media\MediaService;
use Illuminate\Support\Facades\Storage;

class DigitalDownloadController extends Controller
{
   public function __construct(protected MediaService $mediaService) {}
   public function upload(Request $request)
   {

      try {
         $file = $request->file;

         $data = $this->mediaService->storePrivateFile($file, 'private');

         $temp = UploadTemp::create([
            'filename' => $data['filename'],
            'filepath' => $data['filepath'],
            'filesize' => $data['filesize'],
         ]);

         return ApiResponse::success([
            'filename' => $temp->filename,
            'filepath' => $temp->filepath,
            'filesize' => $temp->filesize
         ]);
      } catch (\Exception $th) {
         return ApiResponse::failed($th->getMessage());
      }
   }
   public function destroy($id)
   {
      $file = DigitalDownload::find($id);
      if ($file) {

         if ($file->download_type == 'file') {
            Storage::disk($file->disk)->delete($file->filepath);
         }
         $file->forceDelete();
      }

      return ApiResponse::success();
   }
}
