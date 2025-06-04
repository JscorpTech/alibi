<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\DeliveryResource;
use App\Models\Delivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class DeliveryController extends Controller
{
    use BaseController;

    public function index(): JsonResponse
    {
        $delivery = DeliveryResource::collection(Delivery::query()->get());

        return $this->success(data: $delivery);
    }
}
