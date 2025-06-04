<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BasketRequest;
use App\Http\Requests\Api\LikeRequest;
use App\Http\Resources\Api\BasketResource;
use App\Models\Basket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{
    use BaseController;

    /**
     * Set product to basket or remove
     *
     * @param LikeRequest $request
     * @return JsonResponse
     * @response array{success:true}
     */
    public function index(BasketRequest $request): JsonResponse
    {
        try {
            $product_id = $request->input('product_id');
            $color = $request->input('color_id');
            $size = $request->input('size_id');
            $count = $request->input('count');

            $check = Auth::user()->baskets()->where([
                'product_id' => $product_id,
                'color_id'   => $color,
                'size_id'    => $size,
            ]);
            if ($check->exists()) {
                $check->first()->update(['count' => $check->first()->count + $count]);
            } else {
                Basket::query()->create([
                    'product_id' => $product_id,
                    'user_id'    => Auth::id(),
                    'color_id'   => $color,
                    'size_id'    => $size,
                    'count'      => $count,
                ]);
            }

            return $this->success(__('product.basket:create'));
        } catch (\Throwable $e) {
            return $this->error(__('product.basket:error'));
        }
    }

    public function editCount(Request $request, $id): JsonResponse
    {
        $request->validate([
            'count' => 'required|integer',
        ]);

        $basket = Basket::findOrField($id);
        $basket->count = $request->input('count');
        $basket->save();

        return $this->success(__('updated'));
    }

    /**
     * Get the products in the basket
     *
     * @return JsonResponse
     * @response ProductResource
     */
    public function get(): JsonResponse
    {
        $basket = Auth::user()->baskets()->orderByDesc('id')->get();

        $price = 0;
        $discount = 0;
        foreach ($basket as $item) {
            $price = $price + $item->getTotalPrice();
            $discount = $discount + $item->getProductDiscountPrice();
        }

        return $this->success(data: [
            'items'          => BasketResource::collection($basket),
            'price'          => ($discount != 0 and $discount != null) ? $discount : $price,
            'total_discount' => $discount,
            'discount'       => ($discount != 0 and $discount != null) ? $price - $discount : 0,
            'orginal_price'  => $price,
            'count'          => $basket->count(),
        ]);
    }

    /**
     * Remove the product in the basket
     *
     * @param $id
     * @return JsonResponse
     * @response array{success:true}
     */
    public function remove($id): JsonResponse
    {
        try {
            $res = Basket::query()->where(['user_id' => Auth::id(), 'id' => $id]);
            $res->delete();

            return $this->success(__('product.basket:delete'));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Clear all basket items
     *
     * @return JsonResponse
     * @response array{success:true,message:string}
     */
    public function clear(): JsonResponse
    {
        $user = Auth::user();
        $user->baskets()->delete();

        return $this->success(__('product.basket:clear'));
    }
}
