@props([
    "product"
])
<div
    class="shrink-0 w-[calc(100%/2.09)] md:w-[calc(100%/3.33)] lg:w-[calc(100%/4.44)] xl:w-[calc(100%/5.33)]">
    <a x-data="{ isQuickAddVisible: false }"
       href="{{route("show",$product->id)}}"
       class="w-full flex flex-col group">
        <div
            class="relative overflow-hidden"
            @mouseleave="isQuickAddVisible = false;"
            @touchstart.outside="isQuickAddVisible = false;"
        >
            <div
                class="flex w-full coll-image bg-[#f7f7f7] relative overflow-hidden aspect-[187/251] lg:aspect-[3/4]">

                <div class="swiper w-full mx-auto my-auto swiper_card">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide overflow-hidden">
                            <picture class="block object-cover h-full w-full overflow-hidden">
                                @if($product->discount != 0)
                                    <div class="w-full flex justify-start">
                                        <x-sale/>
                                    </div>
                                @endif
                                <img
                                    src="{{Storage::url($product->image)}}"
                                    alt="Studio Sneaker - Black Vintage White"
                                    loading="lazy"
                                    class="block object-cover h-full w-full aspect-[187/251] lg:aspect-[3/4] scale-[1] loading-lazy"
                                />
                            </picture>

                        </div>
                        <div class="swiper-slide overflow-hidden">
                            <picture class="block object-cover h-full w-full overflow-hidden">

                                <img
                                    src="{{Storage::url(isset($product->images[0]) ? $product->images[0]->path : "")}}"
                                    alt=""
                                    loading="lazy"
                                    class="block object-cover h-full w-full aspect-[187/251] lg:aspect-[3/4] scale-[1] loading-lazy"
                                />
                            </picture>

                        </div>
                    </div>
                </div>

            </div>
            <div x-cloak aria-label="Show Variant Options" class="f-adjust-on-gridchange ">


                <div x-ref="button_7321666945233" class="absolute bottom-0 right-0 z-[10]">
                    <button
                        x-show="!isQuickAddVisible"
                        x-cloak
                        aria-label="Show Variant Options"
                        tabindex="-1"
                        type="button"
                        class="p-[1rem] w-[45px] h-[45px]"
                        @mouseover="isQuickAddVisible = true;"
                        @touchstart.prevent="isQuickAddVisible = true;"
                    >
                        <svg
                            width="13"
                            height="16"
                            viewBox="0 0 13 16"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            role="presentation"
                            class="pointer-events-none"
                        >
                            <path
                                d="M12.5 15.5H0.5V5.00025L11 5.00025L12.3001 5.00013L12.5 5.00009V15.5Z"
                                stroke="black" stroke-linejoin="round"></path>
                            <path d="M3.50004 5C3.50004 5 2.99999 1 6.5 1C10 1 9.49999 5 9.49999 5"
                                  stroke="black"></path>
                        </svg>
                    </button>
                </div>
                <div
                    x-ref="variants_7321666945233"
                    class="card-variants h-full  absolute w-full bottom-0 left-0 z-10 transition-[opacity] duration-300 ease-[ease] flex flex-col justify-end"
                    data-variiants-of="7321666945233"
                    :class="isQuickAddVisible ? 'variants-visible' : 'invisible pointer-events-none'"
                >
                    <div
                        class="flex flex-row-reverse justify-center items-center p-[0.75rem] bg-zinc-100/[.9]">
                        <button
                            class="touch_only p-[1rem] -mr-[0.75rem]"
                            @touchstart.prevent="isQuickAddVisible = false;"
                        >
                            <svg
                                width="12"
                                height="12"
                                viewBox="0 0 18 17"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                role="presentation"
                            >
                                <line y1="-0.5" x2="22.6267" y2="-0.5"
                                      transform="matrix(0.70713 -0.707084 0.70713 0.707084 1 17)"
                                      stroke="black"/>
                                <line y1="-0.5" x2="22.6267" y2="-0.5"
                                      transform="matrix(0.70713 0.707084 -0.70713 0.707084 1 1)"
                                      stroke="black"/>
                            </svg>
                        </button>
                        <div class="scroll_hor p-px">

                            @foreach($product->sizes as $size)
                                <button
                                    aria-label="Button"
                                    type="button"

                                    @click.prevent="window.location.href=`{{route("show",$product->id)}}?size={{$size->id}}`"

                                    class="
                    shrink-0 w-[40px] h-[40px] select-none flex justify-center items-center outline outline-1 outline-[#CACACA] py-[0.5rem] text-[10px]

                      hover:outline-black hover:z-10

                  "
                                >

                                    {{$size->name}}

                                </button>

                            @endforeach

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <h3 class="mt-4 text-center font-global_weight uppercase f-adjust-on-gridchange text-xs ">
            {{ $product->name }}
        </h3>
        <span
            class="lg:mb-4 flex flex-col lg:justify-center mt-1 gap-1 text-center w-full f-adjust-on-gridchange text-xs ">


        <span class="text-primary-gray">
          <span class="capitalize line-clamp-1">{!! $product->desc !!}</span>
          <span class="ml-1 text-xs">
          </span>
        </span>



    <div>
          <span class="uppercase text-[400] font-normal text-black">{{  $product->getPrice() }}</span>
        &nbsp;
        @if($product->isDiscount())
            <span class="uppercase text-gray-500 text-[400] font-normal text-black"><del>{{  $product->getProductPrice() }}</del></span>
        @endif
    </div>

    </span>
    </a>


</div>
