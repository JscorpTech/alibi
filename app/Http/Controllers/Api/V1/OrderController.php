<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRequest;
use App\Http\Resources\Api\OrderGroupResource;
use App\Services\Api\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @var $collection AnonymousResourceCollection
 */
class OrderController extends Controller
{
    use BaseController;

    public OrderService $service;

    public function __construct()
    {
        $this->service = new OrderService();
    }

    /**
     * Create Order
     *
     * @param OrderRequest $request
     * @return JsonResponse
     * @response array{success:true}
     */
    public function index(OrderRequest $request): JsonResponse
    {
        try {
            $this->service->create($request);

            return $this->success(__('product.order:create'));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());

            return $this->error(__('product.order:error'));
        }
    }

    /**
     * Get All Orders
     *
     * @return JsonResponse
     * @response array{success:true,data:AnonymousResourceCollection<OrderResource>}
     */
    public function get(): JsonResponse
    {
        $basket = Auth::user()->OrderGroup()->orderByDesc('id')->get();

        return $this->success(data: OrderGroupResource::collection($basket));
    }

    /**
     * Cancel Order
     *
     * @param $id
     * @return JsonResponse
     * @response array{success:true,message:"string",code:int}
     */
    public function cancel($id): JsonResponse
    {
        try {
            Auth::user()->orders()->where(['id' => $id])->update(['status' => OrderStatusEnum::CANCELED]);

            return $this->success(__('product.order:cancel'));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
}
