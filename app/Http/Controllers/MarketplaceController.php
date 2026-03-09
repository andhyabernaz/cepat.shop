<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use App\Models\ProductLink;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Media\MediaService;
use App\Http\Requests\MarketplaceRequest;

class MarketplaceController extends Controller
{
    public function __construct(protected MediaService $mediaService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Marketplace::get();
        return ApiResponse::success($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MarketplaceRequest $request)
    {
        $filedata = $this->mediaService->storeFile($request->icon);

        $validated = $request->validated();
        $validated['icon_path'] = $filedata['filepath'];

        $data = Marketplace::create($validated);

        return ApiResponse::success($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MarketplaceRequest $request, string $id)
    {

        $validated = $request->validated();
        $config = Marketplace::findOrFail($id);

        if ($request->icon) {
            if($config->icon_path) {
                $this->mediaService->deleteFile($config->icon_path);
            }
            $filedata = $this->mediaService->storeFile($request->icon);
            $validated['icon_path'] = $filedata['filepath'];
        }


        $data = $config->update($validated);

        return ApiResponse::success($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $config = Marketplace::findOrFail($id);

        if ($config->icon_path) {
            $this->mediaService->deleteFile($config->icon_path);
        }
        DB::table('product_links')->where('marketplace_id', $id)->delete();
        $config->delete();
        return ApiResponse::success();
    }

    public function setStatus(Request $request, $id)
    {
        Marketplace::where('id', $id)->update(['is_active' => $request->status]);
        return ApiResponse::success();
    }
}
