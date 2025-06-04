<?php

namespace App\Http\Requests\Filters;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class ProductFilter extends FormRequest
{
    use BaseRequest;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Categoriya bo'yicha filter qilish
            'category_id' => ['integer'],

            // Sub kategoriya bo'yicha filter qilish
            'subcategory_id' => ['integer'],

            /**
             * Narx
             * qidirish uchun qiymet verqul bilan beriladi
             * @example 200000,350000
             */
            'price' => ['min:1', 'max:255'],

            /**
             * Qidirish uchun
             *
             * @example Oyoq kiyim
             */
            'search' => ['max:255'],

            /**
             * Tartiblash uchun
             *
             * @example id,price,created_at,discount,count
             */
            'sort' => ['string','in:id,price,views,created_at,discount,count'],

            /**
             * sort uchun tur yani pastdan tepagami yoki tepadan pastga salarash
             * agar price berilsa desc|asc qimmat boshlash
             *
             * @example desc|asc
             */
            'sort_by' => ['string'],
        ];
    }
}
