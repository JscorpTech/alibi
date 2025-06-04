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
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @response array{success:true,data:ProductResource,meta:array{is_basket:bool,is_save:bool,products:array{category:array{id:int,name:string},data:AnonymousResourceCollection<ProductResource>},sizeInfo:array{image_1:string,image_2:string,name:string}}}
     */
    public function view(Request $request, $id): JsonResponse
    {
        $product = Product::findOrField($id);
        $product->views = $product->views + 1;
        $product->save();
        $product = ProductResource::make($product);
        $categories = $this->service->getOffers($product);
        $products = [];
        foreach ($categories as $category) {
            $products[] = [
                'category' => [
                    'id'   => $category->id,
                    'name' => $category->name,
                ],
                'data' => ProductListResource::collection($category->products),
            ];
        }

        $sizeInfo = $product->sizeImage ?? SizeInfo::query()->first();
        $meta = [
            'products' => $products,
            'sizeInfo' => $sizeInfo,
            'is_basket' => Auth::guard("sanctum")->user()?->baskets()->where(['product_id' => $id])->exists() ?? false,
            'is_save'   => Auth::guard("sanctum")->user()?->likes()->where(['product_id' => $id])->exists() ?? false,
        ];

        return $this->success(data: $product, meta: $meta);
    }

    public function getMeta(Request $request, $id): JsonResponse
    {
        $response = [
            'is_basket' => Auth::user()?->baskets()->where(['product_id' => $id])->exists() ?? false,
            'is_save'   => Auth::user()?->likes()->where(['product_id' => $id])->exists() ?? false,
        ];

        return $this->success(data: $response);
    }

    public function is_already(IsAlreadyRequest $request, $id): JsonResponse
    {
        $data = [
            'product_id' => $id,
        ];
        if ($request->has('color_id') and $request->get('color_id') != null) {
            $data['color_id'] = $request->get('color_id');
        }
        if ($request->has('size_id') and $request->get('size_id') != null) {
            $data['size_id'] = $request->get('size_id');
        }
        $product = ProductOption::query()->where($data);
        if ($product->count() > 1) {
            return $this->success(__('Maxsulot topildi'), [
                'count' => (int) $product->sum('count'),
            ]);
        } else {
            return $this->error(__('Maxsulot mavjud emas'));
        }
    }
}
