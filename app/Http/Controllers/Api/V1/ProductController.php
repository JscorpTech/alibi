<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IsAlreadyRequest;
use App\Http\Requests\Filters\ProductFilter;
use App\Http\Resources\Api\Product\ProductListResource;
use App\Http\Resources\Api\Product\ProductResource;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\SizeInfo;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * @var $collection AnonymousResourceCollection
 */
class ProductController extends Controller
{
    use BaseController;

    public ProductService $service;

    public function __construct()
    {
        $this->service = new ProductService();
        $this->middleware('auth:sanctum', ['only' => ['getMeta']]);
    }

    /**
     * Get All Products
     *
     * @param ProductFilter $filter
     * @return JsonResponse
     * @response array{success:true,data:AnonymousResourceCollection<ProductListResource>}
     */
    public function index(ProductFilter $filter): JsonResponse
    {
        $products = $this->service->index($filter);

        return $this->success(data: $products['data'], meta: $products['meta']);
    }

    /**
     * Filter Products
     *
     * @param ProductFilter $filter
     * @return JsonResponse
     * @response array{success:true,data:AnonymousResourceCollection<ProductListResource>}
     */
    public function filter(ProductFilter $filter): JsonResponse
    {
        $products = $this->service->index($filter);

        return $this->success(data: $products['data'], meta: $products['meta']);
    }

    /**
     * One product view
     *
     * GET /api/v1/products/{id}
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     * @throws Exception
     * @response array{
     *   success:true,
     *   data:ProductResource,
     *   meta:array{
     *     is_basket:bool,
     *     is_save:bool,
     *     products:array<int, array{
     *       category:array{id:int,name:string},
     *       data:AnonymousResourceCollection<ProductListResource>
     *     }>
     *   }
     * }
     */

    public function view(Request $request, $id): JsonResponse
    {
        $product = Product::query()
            ->with([
                'variants:id,product_id,sku,barcode,price,stock,attrs',
                'brand',
                'tags',
                // ✅ UDALIT' LEGACY (esli ne nuzhno):
                // 'options.items', 'colors', 'sizes', 'images',
            ])
            ->findOrField($id);

        $product->increment('views');

        $categories = $this->service->getOffers($product);
        $resource = ProductResource::make($product);

        $products = [];
        foreach ($categories as $category) {
            $products[] = [
                'category' => ['id' => $category->id, 'name' => $category->name],
                'data' => ProductListResource::collection($category->products),
            ];
        }

        $user = Auth::guard('sanctum')->user();
        $meta = [
            'products' => $products,
            'is_basket' => $user?->baskets()->where('product_id', $id)->exists() ?? false,
            'is_save' => $user?->likes()->where('product_id', $id)->exists() ?? false,
        ];

        return $this->success(data: $resource, meta: $meta);
    }


    public function getMeta(Request $request, $id): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // ✅ ULUCHSHENIYE: Odin zapros vmesto dvuh
        $response = [
            'is_basket' => false,
            'is_save' => false,
        ];

        if ($user) {
            $basket = $user->baskets()->where('product_id', $id)->exists();
            $save = $user->likes()->where('product_id', $id)->exists();

            $response = [
                'is_basket' => $basket,
                'is_save' => $save,
            ];
        }

        return $this->success(data: $response);
    }

    // App/Http/Controllers/Api/V1/ProductController.php

    public function is_already(IsAlreadyRequest $request, $id): JsonResponse
    {
        $sizeName = $request->filled('size_id')
            ? \DB::table('sizes')->where('id', $request->integer('size_id'))->value('name')
            : trim((string) $request->query('size', ''));

        $colorName = $request->filled('color_id')
            ? \DB::table('colors')->where('id', $request->integer('color_id'))->value('name')
            : trim((string) $request->query('color', ''));

        $q = \DB::table('variants')->where('product_id', (int) $id);

        // MySQL JSON filter
        if ($sizeName !== '')
            $q->whereRaw("JSON_EXTRACT(attrs, '$.Size') = ?", [$sizeName]);
        if ($colorName !== '')
            $q->whereRaw("JSON_EXTRACT(attrs, '$.Color') = ?", [$colorName]);

        $count = (int) $q->sum('stock');

        // ✅ DOBAVIT' LOGGING (optional)
        if ($count <= 0) {
            \Log::info('Product not available', [
                'product_id' => $id,
                'size' => $sizeName,
                'color' => $colorName,
            ]);
        }

        return $count > 0
            ? $this->success(__('Maxsulot topildi'), ['count' => $count])
            : $this->error(__('Maxsulot mavjud emas'));
    }
}
