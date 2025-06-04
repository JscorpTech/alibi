@php use App\Enums\DiscountEnum;use App\Models\ProductOption;use Illuminate\Support\Facades\Auth;use Illuminate\Support\Facades\Cookie;use Illuminate\Support\Facades\Storage; @endphp
@php
    function isAlready($product,$color) {
         return ProductOption::query()->where(['product_id'=>$product->id,"color_id"=>$color])->where("count", ">=", 1)->exists();
    }
    $image_check = true;
@endphp
<div
    x-data="{
        is_error:false,
        message:'',
        loading:false,


        setError:(message)=>{
            $data.is_error = true;
            $data.message  = message;
        }
    }"

    x-init="document.addEventListener('success',function(){
        $data.success = true;
        $data.is_open = false;
    });
    "
    class="pi-inner-wrapper text-[12px] my-[32px] flex flex-row flex-wrap justify-between items-start w-full">

    <div x-transition x-show="loading"
         class="z-[999999999999999999999999999999989] flex justify-center backdrop-blur-md bg-white  overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <h1 class="text-[25px]">Loading...</h1></div>


    <div
        class="flex flex-wrap items-center w-full mb-[15px] [@media(max-width:1023px)]:py-[15px] [@media(max-width:1023px)]:min-h-[60px] [@media(max-width:1023px)]:mb-0">
        <h1 class="font-global_weight uppercase w-[50%]">




                                                    <span class="flex flex-row items-start relative">
                                                        <span>{{ $product->name }}</span>
                                                    </span>
        </h1>


        <div class="inline-flex w-[50%] justify-end">
                                                    <span class="mr-2 text-primary-gray line-through"
                                                          data-variant-price-compare></span>

            <div>
                                                      <span
                                                          class="uppercase text-[400] font-normal text-black"
                                                          data-uw-rm-sr="">{{ $product->getPrice() }}</span>
                @if($product->isDiscount())
                    &nbsp;
                    <del
                        class="uppercase text-gray-500 !font-bold text-[0.75rem] font-normal text-black"
                        data-uw-rm-sr="">{{ $product->getProductPrice() }}</del>
                @endif
            </div>
        </div>
    </div>

    <div x-cloak="" x-show="is_error"
         class="mt-5 mb-5 w-full bg-orange-100 border-l-4 border-red-500 text-orange-700 p-4"
         role="alert">
        <p x-text="message"></p>
    </div>

    @if(Auth::user()?->is_first_order and Cookie::get("is_first_order",true) !== "false" and ($product->discount == 0 or $product->discount == null))
        <div class="mb-5 w-full bg-gray-100 border-l-4 border-gray-500 text-black p-4"
             role="alert">
            <p>{{ __("first:order:discount",["discount"=>DiscountEnum::FIRST_ORDER]) }}</p>
        </div>
    @endif

    <div
        class="mb-[32px] flex flex-row flex-wrap items-center justify-between w-full">
        <div class="flex flex-row mb-[32px] justify-between w-full">
            <div class="flex flex-col gap-2"></div>

            <div class="wishes-container"
                 data-product-title="Utility Carpenter Jacket - Vintage Brown">
                <div class="wishes">

                    <button
                        aria-label="Add to Wishlist"
                        @click="@can('auth') $wire.addWishlist({{$product->id}}) @else wishlistPopup = true; noScroll = true; @endif "
                        class="cursor-pointer flex flex-row items-center gap-2 underline text-center text-[#575757] text-[10px]">


                        @if(!$is_basket)
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 role="presentation" width="18" height="18"
                                 viewBox="0 0 18 18" fill="none">
                                <path fill="#000"
                                      d="M3.647 0v18L9 13.725 14.353 18V0H3.647ZM13.5 16.2 9 12.6l-4.5 3.6V.9h9v15.3Z"/>
                            </svg>
                        @else
                            <svg width="18" height="18" viewBox="0 0 26 43"
                                 fill="none" xmlns="http://www.w3.org/2000/svg"
                                 role="presentation">
                                <path
                                    d="M12.2816 31.8696L0.5 41.9165V0.5H25.5V41.9576L12.919 31.8601L12.5964 31.6011L12.2816 31.8696Z"
                                    fill="black" stroke="black"/>
                            </svg>
                        @endif
                    </button>

                </div>
            </div>

        </div>


        <div class="flex flex-row justify-between items-center mb-[15px] w-full">
            <div class="">
									<span class="mr-4 text-[12px] font-global_weight text-black relative">{{__("select:color:2")}}<sup
                                            class="opacity-75"></sup>

									</span>
                <span class="text-[12px] ml-2 text-black" data-variant-name="" x-text="color_name"></span>
            </div>
        </div>
        <div class="color-variant-selectors w-full">
            <div class="swiper swatch-side-scroll" id="product-colors">
                <div class="swiper-wrapper p-[1px] flex flex-row">
                    @foreach($product->colors as $color)
                        <div>
                            {{--                            style="filter: grayscale(80%);cursor: not-allowed"--}}
                            @php
                                $check = isAlready($product,$color->color->id);
                            @endphp
                            <div class="relative w-full overflow-hidden justify-center flex items-center">
                                @if(!$check)
                                    <div
                                        class="z-[9999] absolute w-[200%] border-b-[1px] border-black rotate-[54deg]"></div>
                                @endif
                                <div
                                    @if($check) x-on:click="color={{$color->color->id}};color_name=`{{$color->color->name}}`;"
                                    wire:click="$dispatch('color',{value:{{$color->color->id}}})"
                                    @endif
                                    @if(!$check) style="filter: brightness(0.5);cursor: not-allowed" @endif
                                    :class="color === {{$color->color->id}} ? 'selected' : ''"
                                    class="swatch swiper-slide">
                                    <div class="flex justify-center items-center h-[86px] w-full"
                                         style="background: url({{Storage::url($color->image->path ?? '')}}) center center / cover no-repeat; transition: all 0s ease 0s;"
                                         data-handle="studio-sneaker-black-vintage-white"
                                         data-title="Studio Sneaker - Black Vintage White">

                                        <img
                                            src="{{Storage::url($color->image->path ?? '')}}"
                                            alt="Studio Sneaker" title="Studio Sneaker" width="68" height="68"
                                            loading="lazy"
                                            style="opacity: 0;">
                                    </div>
                                </div>
                            </div>
                            @php
                                $image_check = !$check;
                            @endphp
                        </div>

                    @endforeach


                </div>
            </div>
        </div>


        {{-- <div class="color-variant-selectors w-full">--}}

        {{--            <style>--}}


        {{--                .color-picker-wrapper label {--}}
        {{--                    display: block;--}}
        {{--                    margin-bottom: 20px;--}}
        {{--                    color: #000;--}}
        {{--                }--}}

        {{--                .color-picker-wrapper .color-palette {--}}
        {{--                    margin: 0;--}}
        {{--                    padding: 0;--}}
        {{--                    list-style: none;--}}
        {{--                    position: relative;--}}
        {{--                }--}}

        {{--                .color-picker-wrapper .color-palette li {--}}
        {{--                    overflow: hidden;--}}
        {{--                    width: 34px;--}}
        {{--                    height: 34px;--}}
        {{--                    margin: 4px;--}}
        {{--                    display: inline-block;--}}
        {{--                    cursor: pointer;--}}
        {{--                    border-radius: 50%;--}}
        {{--                    box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.1);--}}
        {{--                    transition: all 0.4s ease-in-out;--}}
        {{--                }--}}

        {{--                .color-picker-wrapper .color-palette li.selected {--}}
        {{--                    border-top-right-radius: 0;--}}
        {{--                    border-color: inhert;--}}
        {{--                }--}}

        {{--                .color-picker-wrapper .color-palette li span {--}}
        {{--                    width: 100%;--}}
        {{--                    height: 100%;--}}
        {{--                    display: block;--}}
        {{--                }--}}

        {{--                .color-picker-wrapper .color-palette li img {--}}
        {{--                    vertical-align: middle;--}}
        {{--                }--}}

        {{--            </style>--}}
        {{--            <div class="color-picker-wrapper">--}}
        {{--                <label>{{__("select:color")}}</label>--}}
        {{--                <ul class="color-palette">--}}

        {{--                    @foreach($product->colors as $color)--}}
        {{--                        <li @click="color={{$color->id}}" :class="color === {{$color->id}} ? 'selected' : ''">--}}
        {{--                            <span--}}
        {{--                                style="background-color: {{$color->color}};"></span>--}}
        {{--                        </li>--}}
        {{--                    @endforeach--}}

        {{--                </ul>--}}
        {{--            </div><!-- /color-picker-wrapper -->--}}

        {{--        </div>--}}

        <div class="w-full" data-pdp-form
             data-product-url="https://representclo.com/products/utility-carpenter-jacket-vintage-brown">
            <form method="post" action="/cart/add" accept-charset="UTF-8"
                  class="form product-form w-full" enctype="multipart/form-data"
                  prodid="7311779954897" novalidate="novalidate"><input
                    type="hidden" name="form_type" value="product"/><input
                    type="hidden" name="utf8" value="✓"/>
                <div data-product-select data-currency="USD"
                     data-currency-symbol="$">
                    <select id="ProductSelectData-7311779954897" name="id"
                            class="product-select-data hidden">


                        <option
                            value="41805153272017"
                            data-variant-title="XS / Vintage Brown"
                            available="true"
                            data-variant-price="40500"
                            data-variant-price-compare=""
                            selected


                            data-option-1="XS"


                            data-option-2="Vintage Brown"


                        >XS / Vintage Brown
                        </option>


                        <option
                            value="41805153304785"
                            data-variant-title="S / Vintage Brown"
                            available="true"
                            data-variant-price="40500"
                            data-variant-price-compare=""


                            data-option-1="S"


                            data-option-2="Vintage Brown"


                        >S / Vintage Brown
                        </option>


                        <option
                            value="41805153337553"
                            data-variant-title="M / Vintage Brown"
                            available="true"
                            data-variant-price="40500"
                            data-variant-price-compare=""


                            data-option-1="M"


                            data-option-2="Vintage Brown"


                        >M / Vintage Brown
                        </option>


                        <option
                            value="41805153370321"
                            data-variant-title="L / Vintage Brown"
                            available="true"
                            data-variant-price="40500"
                            data-variant-price-compare=""


                            data-option-1="L"


                            data-option-2="Vintage Brown"


                        >L / Vintage Brown
                        </option>


                        <option
                            value="41805153403089"
                            data-variant-title="XL / Vintage Brown"
                            available="true"
                            data-variant-price="40500"
                            data-variant-price-compare=""


                            data-option-1="XL"


                            data-option-2="Vintage Brown"


                        >XL / Vintage Brown
                        </option>


                        <option
                            value="41805153435857"
                            data-variant-title="XXL / Vintage Brown"
                            available="true"
                            data-variant-price="40500"
                            data-variant-price-compare=""


                            data-option-1="XXL"


                            data-option-2="Vintage Brown"


                        >XXL / Vintage Brown
                        </option>

                    </select>
                </div>
                <div class="quantity-wrapper mx-[6px] hidden">
                    <span class="qty-control qty-dec"></span>
                    <input type="number" name="quantity" value="1" min="1"
                           pattern="[0-9]*" readonly="readonly">
                    <span class="qty-control qty-inc"></span>
                </div>
                <div class="variant-options-wrapper ">
                    <div
                        class="w-full flex justify-between items-center my-[15px] ">
                        <div>


                        </div>

                    </div>


                    @if($product->sizeImage != null)
                        <div class="w-full flex justify-end items-center my-[15px] ">
                            <div>
                                <span x-on:click="modal.open=true"
                                      class="text-[12px] font-normal text-primary-gray cursor-pointer">Size & Fit Guide</span>
                            </div>
                        </div>
                    @endif

                    <div
                        class="option-selector grid grid-cols-7 lg:px-0 optionWrapper gap-px m-px product-size-button "
                    >
                        @foreach($product->sizes as $size)
                            @php
                                $check = $image_check ? true : (count($already['size']) != 0 ?!in_array($size->id,$already['size']) : false);
                            @endphp
                            <div class="relative w-full overflow-hidden justify-center flex items-center">
                                @if($check)
                                    <div class="z-[9999] absolute w-[200%] border-b-[1px] border-black rotate-45"></div>
                                @endif
                                <button
                                    type="button"
                                    wire:click="$dispatch('size',{value:{{$size->id}}})"
                                    @if(!$check)
                                        x-on:click="size = {{$size->id}};"
                                    @endif
                                    aria-label="{{$size->name}}"
                                    data-option-type="Size"
                                    data-option-index="1"
                                    data-value="{{$size->name}}"
                                    :style="size == {{$size->id}} ? 'border: 1px solid black;' : ''"
                                    class="uppercase w-full @if($check) bg-gray-200 @endif js-select-variant box sizeSelector flex justify-center items-center text-xs outline outline-1 "
                                >
                                    {{$size->name}}
                                </button>
                            </div>
                        @endforeach

                    </div>


                    <div
                        class="w-full flex justify-between items-center my-[15px] hidden">
                        <div>

                                                                    <span
                                                                        class="text-[12px] font-normal text-primary-gray">{{__("select:color")}}</span>

                        </div>

                    </div>


                    <div
                        class="option-selector grid grid-cols-7 lg:px-0 optionWrapper gap-px m-px product-size-button hidden"
                    >

                        <button
                            type="button"
                            aria-label="Vintage Brown"
                            data-option-type="Colour"
                            data-option-index="2"
                            data-value="Vintage Brown"
                            class="js-select-variant box sizeSelector flex justify-center items-center text-xs outline outline-1 selected"
                        >
                            Vintage Brown
                        </button>

                    </div>


                </div>
                <div class="notify-me-wrapper hidden mt-4">
                    <a aria-label="Notify me when back in stock"
                       id="swym-custom-bis-modal-button"
                       href="javascript:void(0)"
                       class="swym-bis-universal text-xs underline text-primary-gray w-full lg:mt-4 lg:p-0 pt-4"
                       data-product-url="https://representclo.com/products/utility-carpenter-jacket-vintage-brown"
                    >
                        Size Not In Stock?
                    </a>
                </div>

                <div data-js-intersection></div>

                <div class="add-to-cart-wrapper">


                    @can("auth")
                        <button
                            aria-label="Submit"
                            type="button"
                            x-on:click="()=>{

                                if(color == null){
                                    setError(`{{__("select:color")}}`)
                                    return;
                                }

                                 if(size == null){
                                    setError(`{{__("select:size")}}`)
                                    return;
                                }

                                pay_modal = true;
                               }"
                            name="add"
                            class="mt-4 mb-[14px] w-full overflow-hidden js-add-product h-[52px] text-xs bg-[#000000] items-center justify-center flex font-global_weight text-center text-white hover:bg-[#000000] cursor-pointer uppercase"
                        >
                                                                <span class="btn-stat-text absolute"
                                                                      data-text="Add to Cart">{{__("create:order")}}</span>
                        </button>
                    @else
                        <a
                            href="{{route("auth")}}"
                            aria-label="Submit"
                            type="button"
                            name="add"
                            class="mt-4 mb-[14px] w-full overflow-hidden js-add-product h-[52px] text-xs bg-[#000000] items-center justify-center flex font-global_weight text-center text-white hover:bg-[#000000] cursor-pointer uppercase"
                        >
                                                                <span class="btn-stat-text absolute"
                                                                      data-text="Add to Cart">{{__("create:order")}}</span>
                        </a>
                    @endcan
                </div>
                <div class="additional-checkout-buttons pb-[14px] hidden">
                    <div data-shopify="payment-button"
                         data-has-selling-plan="false"
                         data-has-fixed-selling-plan="false"
                         class="shopify-payment-button">
                        <button
                            class="shopify-payment-button__button shopify-payment-button__button--unbranded shopify-payment-button__button--hidden"
                            disabled="disabled" aria-hidden="true"></button>
                        <button
                            class="shopify-payment-button__more-options shopify-payment-button__button--hidden"
                            disabled="disabled" aria-hidden="true"></button>
                    </div>
                </div>


                <input type="hidden" name="product-id" value="7311779954897"/>
            </form>
        </div>
    </div>
</div>
