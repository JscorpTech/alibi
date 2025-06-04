<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\SubCategoryResource;
use App\Http\Resources\CategoryProductResource;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use BaseController;

    /**
     * Get All Categories
     *
     * @return JsonResponse
     * @response array{success:true,data:AnonymousResourceCollection<CategoryResource>}
     */
    public function index(): JsonResponse
    {
        $categories = CategoryResource::collection(Category::query()->get());

        return $this->success(data: $categories);
    }

    public function subCategory($id): JsonResponse
    {
        $categories = SubCategory::query()
            ->where(['category_id' => Category::findOrField($id)->id])
            ->orderBy('position')
            ->get();

        return $this->success(data: SubCategoryResource::collection($categories));
    }

    public function categoryProducts(Request $request): JsonResponse
    {
        $categories = Category::query()->get();

        return $this->success(data: CategoryProductResource::collection($categories));
    }
}
