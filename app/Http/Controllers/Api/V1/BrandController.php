<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    use BaseController;

    function index(): JsonResponse
    {
        $brands = Brand::query()->get();
        return $this->success(data: BrandResource::collection($brands));
    }
}
