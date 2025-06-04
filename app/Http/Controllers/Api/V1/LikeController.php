<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LikeRequest;
use App\Http\Resources\Api\Product\ProductListResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @var $collection AnonymousResourceCollection
 */
class LikeController extends Controller
{
    use BaseController;

    /**
     * Set product to basket or remove
     *
     * @param LikeRequest $request
     * @return JsonResponse
     * @response array{success:true}
     */
    public function index(LikeRequest $request): JsonResponse
    {
        try {
            $product_id = $request->input('product_id');
            $check = Auth::user()->likes()->where(['product_id' => $product_id]);

            if ($check->exists()) {
                return $this->error(__('product.save:already'));
            }

            Auth::user()->likes()->attach($product_id);

            return $this->success(__('product.save:create'));
        } catch (\Throwable $e) {
            return $this->error(__('product.save:error'));
        }
    }

    /**
     * All liked products
     *
     * @return JsonResponse
     * @response array{success:true,data:AnonymousResourceCollection<ProductResource>}
     */
    public function get(): JsonResponse
    {
        $likes = Auth::user()->likes()->orderByDesc('id')->get();

        return $this->success(data: ProductListResource::collection($likes));
    }

    /**
     * Remove from liked products
     *
     * @param $id
     * @return JsonResponse
     * @response array{success:true}
     */
    public function remove($id): JsonResponse
    {
        try {
            Auth::user()->likes()->detach($id);

            return $this->success(__('product.save:detach'));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
}
