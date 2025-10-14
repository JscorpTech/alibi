<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BasketRequest;
use App\Http\Resources\Api\BasketResource;
use App\Models\Basket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{
    use BaseController;

    /**
     * Добавить/увеличить товар в корзину по variant_id
     */
    public function index(BasketRequest $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $productId = (int) $request->input('product_id');
            $variantId = (int) $request->input('variant_id');
            $count = (int) $request->input('count');

            // ищем такую же позицию (product_id + variant_id)
            $row = Basket::query()
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            if ($row) {
                $row->increment('count', $count);
            } else {
                Basket::query()->create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'count' => $count,
                ]);
            }

            return $this->success(__('product.basket:create'));
        } catch (\Throwable $e) {
            return $this->error(__('product.basket:error'));
        }
    }

    /**
     * Изменить количество конкретной записи корзины
     */
    public function editCount(Request $request, $id): JsonResponse
    {
        $request->validate([
            'count' => ['required', 'integer', 'min:1'],
        ]);

        $basket = Basket::where('user_id', Auth::id())->findOrField($id);
        $basket->update(['count' => (int) $request->input('count')]);

        return $this->success(__('updated'));
    }

    /**
     * Получить корзину пользователя + агрегаты
     */
    public function get(): JsonResponse
    {
        // желательно подгружать product/variant, чтобы ресурс не делал N+1
        $basket = Auth::user()
            ->baskets()
            ->with([
                'product:id,name_ru,price,discount,image,gallery,is_active',
                'variant:id,product_id,sku,barcode,price,stock,attrs',
            ])
            ->orderByDesc('id')
            ->get();

        // считаем суммы с учётом приоритета цены варианта
        $original = 0;
        $discounted = 0;

        foreach ($basket as $item) {
            $line = $item->getLineTotals(); // см. ниже в модели Basket
            $original += $line['original'];
            $discounted += $line['discounted'];
        }

        return $this->success(data: [
            'items' => BasketResource::collection($basket),
            'price' => $discounted ?: $original,   // текущая цена (со скидкой, если есть)
            'total_discount' => $discounted,                // можно трактовать как "сумма со скидкой"
            'discount' => $discounted ? ($original - $discounted) : 0,
            'orginal_price' => $original,
            'count' => $basket->count(),
        ]);
    }

    /**
     * Удалить позицию из корзины
     */
    public function remove($id): JsonResponse
    {
        try {
            Basket::query()
                ->where('user_id', Auth::id())
                ->where('id', $id)
                ->delete();

            return $this->success(__('product.basket:delete'));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Очистить корзину
     */
    public function clear(): JsonResponse
    {
        Auth::user()->baskets()->delete();
        return $this->success(__('product.basket:clear'));
    }
}