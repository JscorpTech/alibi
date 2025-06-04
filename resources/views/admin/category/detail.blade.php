@php use Illuminate\Support\Facades\Storage; @endphp
<x-layouts.main>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="product-slider" id="galleryTop">
                        <div class="swiper-container theme-slider position-lg-absolute all-0"
                             data-swiper='{"autoHeight":true,"spaceBetween":5,"loop":true,"loopedSlides":5,"thumb":{"spaceBetween":5,"slidesPerView":5,"loop":true,"freeMode":true,"grabCursor":true,"loopedSlides":5,"centeredSlides":true,"slideToClickedSlide":true,"watchSlidesVisibility":true,"watchSlidesProgress":true,"parent":"#galleryTop"},"slideToClickedSlide":true}'>
                            <div class="swiper-wrapper h-100">
                                <div class="swiper-slide h-100">
                                    <img class="rounded-1 object-fit-cover h-100 w-100"
                                         src="{{Storage::url($product->image)}}" alt=""/>
                                </div>

                                @foreach($product->images as $image)
                                    <div class="swiper-slide h-100">
                                        <img class="rounded-1 object-fit-cover h-100 w-100"
                                             src="{{Storage::url($image->path)}}" alt=""/>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-nav">
                                <div class="swiper-button-next swiper-button-white"></div>
                                <div class="swiper-button-prev swiper-button-white"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h5>{{$product->name}}</h5><a class="fs-10 mb-2 d-block" href="#!">{{$product->categoryNames()}}
                        & {{__($product->gender)}}</a>

                    <p class="fs-10">{!! $product->desc !!}</p>
                    <h4 class="d-flex align-items-center"><span class="text-warning me-2">{{$product->discount != null ? number_format($product->discount) : number_format($product->price)}} so'm</span>
                        @if($product->discount != null)
                            <span class="me-1 fs-10 text-500"><del class="me-1">{{number_format($product->price)}} so'm</del></span>
                        @endif
                    </h4>
{{--                    <p class="fs-10 mb-1">--}}
{{--                        <span>{{__("product.count")}}: </span><strong>{{$product->count}} {{__("ta")}}</strong></p>--}}
                    <p class="fs-10 mb-5">{{__("product.status")}}: <strong
                                class="text-success">{{__($product->status)}}</strong></p>

                    <div class="row d-flex justify-content-end">
                        <div class="col-auto px-0">
                            <x-far-button icon="fa-heart" :text="$likes" color="danger"/>
                            <x-far-button icon="fa-edit" :href="route('product.edit',$product->id)" color="warning"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="mt-4">
                        <div>
                            <table class="table fs-10 mt-3">
                                <tbody>

                                @foreach($items as $item)
                                    <tr>
                                        <td class="bg-100" style="width: 30%;">{{$item['label']}}</td>
                                        <td>{{$item['value']}}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot:ext_footer>
        <script src="{{asset("vendors/swiper/swiper-bundle.min.js")}}"></script>
        <script src="{{asset("vendors/rater-js/index.js")}}"></script>
    </x-slot:ext_footer>
    <x-slot:ext_header>
        <link rel="stylesheet" href="{{asset("vendors/swiper/swiper-bundle.min.css")}}">
    </x-slot:ext_header>
</x-layouts.main>
