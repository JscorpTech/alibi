@php use App\Enums\ProductStatusEnum;use App\Enums\RoleEnum;use Illuminate\Support\Facades\Storage; @endphp
<x-layouts.main>

    <style>
        .product__image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }

        .product__price {
            color: var(--falcon-warning);
            white-space: nowrap;
        }

        .line__clamp {
            display: -webkit-box;
            overflow: hidden;
            text-overflow: ellipsis;
            -webkit-box-orient: vertical;
        }

        .clamp__2 {
            -webkit-line-clamp: 2;
        }

        .clamp__5 {
            -webkit-line-clamp: 5;
        }

        .clamp__1 {
            -webkit-line-clamp: 1;
        }

        .product__discount {
            opacity: 0.9;
        }

        .product__card {
            cursor: pointer;
        }
    </style>
    <x-com.header-cards
        :cards="[['name'=>__('products.count'),'value'=>$products->total().' '.__('ta'),'image'=>asset('assets/img/icons/spot-illustrations/corner-1.png')],['name'=>__('products.price'),'value'=>number_format($allProducts->sum('price')).' '.__('currency'),'image'=>asset('assets/img/icons/spot-illustrations/corner-2.png')],['name'=>__('products.discount:price'),'value'=>number_format($allProducts->sum('discount')).' '.__('currency'),'image'=>asset('assets/img/icons/spot-illustrations/corner-3.png')]]"
        font="fs-7"/>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row flex-between-center">
                <div class="col-sm-auto mb-2 mb-sm-0">
                    <h6 class="mb-0">{{__("filter")}}</h6>
                </div>
                <div class="col-sm-auto">
                    <div class="row gx-2 align-items-center">
                        <div class="col-auto">
                            <form style="display: flex;gap: 10px">
                                <x-com.select :list="ProductStatusEnum::toArray()" name="status"/>
                                <x-com.button button="btn-outline-primary" :text="__('update')" type="submit"/>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                @foreach($products as $product)
                    <x-com.product.card :product="$product"/>
                @endforeach
            </div>
        </div>
        <x-pagination :object="$products"/>
    </div>
    <x-ext.imask/>
</x-layouts.main>
