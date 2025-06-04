<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Address\DistrictResource;
use App\Http\Resources\Address\RegionResource;
use App\Models\District;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @var $collection AnonymousResourceCollection
 */

/**
 * @tags Address
 */
class AddressController extends Controller
{
    use BaseController;

    /**
     * Get regions
     *
     * @return JsonResponse
     * @response AnonymousResourceCollection<RegionResource>
     */
    public function regions(): JsonResponse
    {
        return $this->success(data: RegionResource::collection(Region::query()->orderByDesc('id')->get()));
    }

    /**
     * Get districts
     *
     * @param $id
     * @return JsonResponse
     * @response AnonymousResourceCollection<DistrictResource>
     */
    public function districts($id): JsonResponse
    {
        return $this->success(data: DistrictResource::collection(District::query()->where('region_id', $id)->orderByDesc('id')->get()));
    }
}
