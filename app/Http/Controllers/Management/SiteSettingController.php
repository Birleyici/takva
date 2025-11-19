<?php

namespace App\Http\Controllers\Management;

use App\DTOs\SiteSettingDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\UpdateSiteSettingRequest;
use App\Services\SiteSettingService;
use Illuminate\Http\JsonResponse;

class SiteSettingController extends Controller
{
    public function __construct(
        private readonly SiteSettingService $siteSettingService
    ) {
    }

    public function show(): JsonResponse
    {
        $settings = $this->siteSettingService->get();

        return response()->json([
            'data' => $settings->toArray(),
        ]);
    }

    public function update(UpdateSiteSettingRequest $request): JsonResponse
    {
        $settings = $this->siteSettingService->update($request->validated());

        return response()->json([
            'message' => 'Ayarlar güncellendi.',
            'data' => $settings->toArray(),
        ]);
    }
}
