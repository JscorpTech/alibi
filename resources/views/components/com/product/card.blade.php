@props([
    "product"
])
<div class="col-12 p-x1 shadow-sm mt-2 product__card">
    <div class="row">
        <div class="col-md-3">
            <a href="{{route("product.show",$product->id)}}">
                <img class="product__image max-[770px]:!w-[500px] max-[770px]:!h-[auto] max-h-[500px]"
                     src="{{Storage::url($product->image)}}" alt="">
            </a>
        </div>
        <div class="col-md-9">
            <div class="row h-100">
                <div class="col-lg-8">
                    <a class="text-decoration-none" href="{{route("product.show",$product->id)}}">
                        <h5 class="product__title clamp__1 max-[770px]:mt-[20px]">{{$product->name}}</h5>
                        <p class="product__desc line__clamp clamp__5">{!! $product->desc !!}</p>
                    </a>
                </div>
                <div class="col-lg-4 d-flex justify-content-between flex-column">
                    <div>
                        <h5 class="product__price">{{$product->discount != 0 ? number_format($product->discount) : number_format($product->price)}} {{__("currency")}}</h5>
                        @if($product->discount)
                            <h6 class="product__discount">
                                <del>{{number_format($product->price)}} {{__("currency")}}</del>
                            </h6>
                        @endif
                        <p>{{__("status")}}: <span class="text-info">{{__($product->status)}}</span>
                        </p>
                    </div>
                    <div class="justify-content-end d-flex">
                        <x-far-button icon="fa-edit" :href="route('product.edit',$product->id)"
                                      :text="__('edit')"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
