<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Product;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Enums\ProductTypeEnum;
use App\Models\DigitalDownload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LicenseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $licenses = License::whereHas('product')->latest('id')->with(['product' => function ($q) {
            $q->select('id', 'title', 'product_type');
            $q->with('assets');
        }])->where('user_id', $user->id)
            ->paginate(10)
            ->withQueryString();

        return ApiResponse::success($licenses);
    }

    public function show(Request $request, $id)
    {
        $license = License::active()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$license) {
            return ApiResponse::failed('Unauthenticated for access this license');
        }

        $product = Product::select('id', 'title', 'product_type')->find($license->product_id);

        if ($product->product_type == ProductTypeEnum::DigitalDownload->value) {
            $product->load('digitalDownloads');
            $product->digitalDownloads->makeHidden(DigitalDownload::CLASSIFIED);
        }
        if ($product->product_type == ProductTypeEnum::DigitalVideo->value) {
            $product->load('digitalVideos');
        }

        $product->license = $license;

        return ApiResponse::success($product);
    }
    public function download(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'license_id' => 'required',
            'item_id' => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::failed('Download has problem, Please contact your web administrator');
        }

        $license = License::active()
            ->where('id', $request->license_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$license) {
            return ApiResponse::failed('You not authenticated for download this file');
        }


        $product = $license->product;

        $file = $product->digitalDownloads->find($request->item_id);

        return Storage::download($file->filepath);
    }
    public function getDownloadUrl($id)
    {

        $data = DigitalDownload::where('id', $id)->first();

        return ApiResponse::success($data->filepath);
    }
    public function checkUserHasLicense(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'product_id' => 'required',
        ]);

        $count = License::where('user_id', $request->user_id)
            ->where('product_id', $request->product_id)
            ->active()
            ->count();

        return response()->json([
            'success' => true,
            'status_license' => $count > 0
        ]);
    }
}
