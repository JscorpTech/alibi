<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\BannerTypeEnum;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BannerResource;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    use BaseController;

    public function index(): JsonResponse
    {
        $banners = BannerResource::collection(
            Banner::query()->where(['type' => BannerTypeEnum::MOBILE])->get()
        );

        return $this->success(data: $banners);
    }
}
