<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\AffiliateConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AffiliateConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cfg = AffiliateConfig::first();

        return ApiResponse::success($cfg);
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
            'description' => 'required',
            'welcome_message' => 'required',
            'suspend_message' => 'required',
            'ttl' => 'required'
        ]);

        $cfg = AffiliateConfig::first();

        $cfg->description = $request->description;
        $cfg->ttl = $request->ttl;
        $cfg->is_active = $request->boolean('is_active');
        $cfg->is_auto_active = $request->boolean('is_auto_active');
        $cfg->save();

        Cache::forget('aff_config');

        return ApiResponse::success($cfg);
    }
}
