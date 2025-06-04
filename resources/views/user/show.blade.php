@php use Illuminate\Support\Facades\Storage; @endphp
@props([
    "product"
])
<x-layouts.user xmlns="">

    @php
        $color = $product->colors()->first()?->color;
    @endphp
    <div x-data="{
            pay_modal:{{ $size == null ? 'null' : $size }},
            is_open:false,
            success:false,
            color_name:'{{$color?->name}}',
            color:{{$color?->id}},
            modal:{open:false},
            size:{{$size == null ? 'null' : $size}},
            count:1,
            price:'{{$product->discount != 0 ? $product->discount : $product->price}}',
    }">

        <main
            id="MainContent"
            role="main"
            tabindex="-1"
            class="-mt-[59px] lg:-mt-[0px]"
            :class="searchOpen ? 'searchIsOpen' : ''"
        >
            <livewire:user.pay-modal :product="$product"/>
            <livewire:user.size-modal :product="$product"/>


            <div id="shopify-section-template--15863087202513__main" class="shopify-section mainproduct">
                <div id="shopify-section-product-template" class="relative z-20">
                    <div class="w-full">
                        <div class="bg-filter"></div>
                        <div class="gallery-zoom-wrapper" id="pdpGalleryZoom">
                            <div class="swiper zoomGalleryCarousel">
                                <div class="gallery-zoom-close fixed top-[30px] right-[30px]">
                                    <svg width="18" height="17" viewBox="0 0 18 17" fill="none"
                                         xmlns="http://www.w3.org/2000/svg" role="presentation">
                                        <line y1="-0.5" x2="22.6267" y2="-0.5"
                                              transform="matrix(0.70713 -0.707084 0.70713 0.707084 1 17)"
                                              stroke="black"></line>
                                        <line y1="-0.5" x2="22.6267" y2="-0.5"
                                              transform="matrix(0.70713 0.707084 -0.70713 0.707084 1 1)"
                                              stroke="black"></line>
                                    </svg>
                                </div>
                                <div class="swiper-wrapper zoom-gallery-container">

                                    <div class="swiper-slide" data-swiper-slide-index="0">
                                        <div class="swiper-zoom-container">
                                            <picture class="block h-full w-full">

                                                <div class="image-obj h-full w-full">
                                                    <img
                                                        sizes="(max-width: 1023px) 100vw, 1920px"
                                                        srcset="{{Storage::url($product->image)}}"
                                                        src="https://sfycdn.speedsize.com/fbaf6506-81e1-43a2-bcc1-80e18c7b0146/https://representclo.com/cdn/shop/files/m2hpvBNhtxSd9Pxe7xamKu6AtwKlW0Q6pSbnSu2KdV8.jpg?v=1700672580&width=992 1x,https://sfycdn.speedsize.com/fbaf6506-81e1-43a2-bcc1-80e18c7b0146/https://representclo.com/cdn/shop/files/m2hpvBNhtxSd9Pxe7xamKu6AtwKlW0Q6pSbnSu2KdV8.jpg?v=1700672580&width=1920 2x"
                                                        alt="Utility Carpenter Jacket - Vintage Brown"
                                                        width="580"
                                                        height="580"
                                                        loading="lazy"
                                                        data-zoom-src="https://sfycdn.speedsize.com/fbaf6506-81e1-43a2-bcc1-80e18c7b0146/https://representclo.com/cdn/shop/files/m2hpvBNhtxSd9Pxe7xamKu6AtwKlW0Q6pSbnSu2KdV8.jpg?v=1700672580&width=1920 1x,https://sfycdn.speedsize.com/fbaf6506-81e1-43a2-bcc1-80e18c7b0146/https://representclo.com/cdn/shop/files/m2hpvBNhtxSd9Pxe7xamKu6AtwKlW0Q6pSbnSu2KdV8.jpg?v=1700672580&width=3840 2x"
                                                        class="block h-full w-full object-cover"
                                                    >
                                                </div>
                                            </picture>
                                        </div>
                                    </div>

                                    @foreach($product->images as $image)
                                        <div class="swiper-slide" data-swiper-slide-index="1">
                                            <div class="swiper-zoom-container">
                                                <picture class="block h-full w-full">

                                                    <div class="image-obj h-full w-full">
                                                        <img
                                                            sizes="(max-width: 1023px) 100vw, 1920px"
                                                            srcset="{{Storage::url($image->path)}}"
                                                            src="{{Storage::url($image->path)}}"
                                                            alt="Utility Carpenter Jacket - Vintage Brown"
                                                            width="580"
                                                            height="580"
                                                            loading="lazy"
                                                            data-zoom-src="https://sfycdn.speedsize.com/fbaf6506-81e1-43a2-bcc1-80e18c7b0146/https://representclo.com/cdn/shop/files/4LhdN6Lqk3Y9-pYRcN54ECGzDA64oIn7wAAPB7uA8Hk.jpg?v=1700672582&width=1920 1x,https://sfycdn.speedsize.com/fbaf6506-81e1-43a2-bcc1-80e18c7b0146/https://representclo.com/cdn/shop/files/4LhdN6Lqk3Y9-pYRcN54ECGzDA64oIn7wAAPB7uA8Hk.jpg?v=1700672582&width=3840 2x"
                                                            class="block h-full w-full object-cover"
                                                        >
                                                    </div>
                                                </picture>
                                            </div>
                                        </div>
                                    @endforeach


                                </div>
                            </div>
                        </div>

                        <div x-data="productPage" class="product-main-wrapper">
                            <div class="main-product-wrapper flex flex-row items-start">
                                <div class="block h-full w-full max-w-[50%]" id="productGallery">
                                    <div class="swiper productGalleryCarousel" style="position:static">
                                        <div class="swiper-wrapper gallery-container" style="z-index: auto">
                                            <div class="swiper-slide" data-swiper-slide-index="0">
                                                <div class="swiper-zoom-container">
                                                    <picture class="block h-full w-full">

                                                        <div class="image-obj h-full w-full">
                                                            <img
                                                                sizes="(max-width: 1023px) 100vw, 960px"
                                                                src="{{Storage::url($product->image)}}"
                                                                alt="Utility Carpenter Jacket - Vintage Brown"
                                                                width="580"
                                                                height="580"
                                                                loading="lazy"
                                                                data-zoom-src="https://sfycdn.speedsize.com/fbaf6506-81e1-43a2-bcc1-80e18c7b0146/https://representclo.com/cdn/shop/files/m2hpvBNhtxSd9Pxe7xamKu6AtwKlW0Q6pSbnSu2KdV8.jpg?v=1700672580&width=1920 1x,https://sfycdn.speedsize.com/fbaf6506-81e1-43a2-bcc1-80e18c7b0146/https://representclo.com/cdn/shop/files/m2hpvBNhtxSd9Pxe7xamKu6AtwKlW0Q6pSbnSu2KdV8.jpg?v=1700672580&width=3840 2x"
                                                                class="block h-full w-full object-cover"
                                                            >
                                                        </div>
                                                    </picture>
                                                </div>
                                            </div>


                                            @foreach($product->images as $image)
                                                <div class="swiper-slide" data-swiper-slide-index="0">
                                                    <div class="swiper-zoom-container">
                                                        <picture class="block h-full w-full">

                                                            <div class="image-obj h-full w-full">
                                                                <img
                                                                    sizes="(max-width: 1023px) 100vw, 960px"
                                                                    src="{{Storage::url($image->path)}}"
                                                                    alt="Utility Carpenter Jacket - Vintage Brown"
                                                                    width="580"
                                                                    height="580"
                                                                    loading="lazy"
                                                                    data-zoom-src="https://sfycdn.speedsize.com/fbaf6506-81e1-43a2-bcc1-80e18c7b0146/https://representclo.com/cdn/shop/files/m2hpvBNhtxSd9Pxe7xamKu6AtwKlW0Q6pSbnSu2KdV8.jpg?v=1700672580&width=1920 1x,https://sfycdn.speedsize.com/fbaf6506-81e1-43a2-bcc1-80e18c7b0146/https://representclo.com/cdn/shop/files/m2hpvBNhtxSd9Pxe7xamKu6AtwKlW0Q6pSbnSu2KdV8.jpg?v=1700672580&width=3840 2x"
                                                                    class="block h-full w-full object-cover"
                                                                >
                                                            </div>
                                                        </picture>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                    <div class="carousel-footer">
                                        <div class="flex flex-row align-center justify-between">
                                            <div
                                                class="mobile-slide-indicator text-[12px] left-[15px] flex items-center leading-none">
                                                <span class="current-slide">1</span><span
                                                    class="px-[8px] text-[8px] text-primary-gray">/</span><span
                                                    class="slide-count">{{$product->images()->count()+1}}</span>
                                            </div>
                                            <div class="mobile-zoom-toggle">
                                                <svg width="18" height="17" viewBox="0 0 18 17" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg" role="presentation"
                                                     class="scale-75 rotate-45">
                                                    <line y1="-0.5" x2="22.6267" y2="-0.5"
                                                          transform="matrix(0.70713 -0.707084 0.70713 0.707084 1 17)"
                                                          stroke="black"></line>
                                                    <line y1="-0.5" x2="22.6267" y2="-0.5"
                                                          transform="matrix(0.70713 0.707084 -0.70713 0.707084 1 1)"
                                                          stroke="black"></line>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="product-info !min-h-[auto] flex flex-row flex-wrap items-end sticky w-full max-w-[472px] mx-auto top-0">
                                    <div
                                        class="centered-block block w-full [@media(max-width:1023px)]:relative [@media(max-width:1023px)]:z-30">
                                        <div class="product-info-container flex flex-row flex-wrap w-full">
                                            <div class="pl-form-wrapper w-full hidden">

                                                <div class="text-[12px] flex flex-wrap items-center">
                                                    <div
                                                        class="flex flex-wrap items-center w-full [@media(max-width:1023px)]:py-[15px] [@media(max-width:1023px)]:min-h-[60px]">
                                                        <h1 class="font-global_weight uppercase w-[50%]">
			<span class="flex flex-row items-start relative">
				<span>Utility Carpenter Jacket</span>
			</span>
                                                        </h1>
                                                        <div class="text-right w-[50%]">
			<span class="font-normal text-black">

				<span class="font-normal text-black [@media(max-width:1023px)]:inline-block">LAUNCHING 08.02.23</span>
			</span>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="w-full mb-[22px] mt-[22px] text-base font-bold text-[14px] [@media(max-width:1023px)]:text-center [@media(max-width:1023px)]:mt-[5px]">

                                                        <div
                                                            class="timer inline-flex"
                                                            data-end="2023-08-02 20:00:00"
                                                            x-data="preLaunchCountdown"
                                                        >
                                                            <span x-text="prelaunchTime().days" class=""></span>
                                                            <span class="mr-1 ">D</span>
                                                            <span class="mr-1 ">:</span>
                                                            <span x-text="prelaunchTime().hours" class=""></span>
                                                            <span class="mr-1 ">H</span>
                                                            <span class="mr-1 ">:</span>
                                                            <span x-text="prelaunchTime().minutes" class=""></span>
                                                            <span class="mr-1 ">M</span>
                                                            <span class="mr-1 ">:</span>
                                                            <span x-text="prelaunchTime().seconds" class=""></span>
                                                            <span class="mr-1 ">S</span>
                                                        </div>

                                                        <script>
                                                            document.addEventListener('alpine:init', () => {
                                                                Alpine.data('preLaunchCountdown', () => ({
                                                                    expiry: new Date(
                                                                        "2023-08-02T20:00:00"
                                                                    ).getTime(),
                                                                    remaining: null,
                                                                    timeLeft: 0,

                                                                    init() {
                                                                        this.setRemaining()
                                                                        setInterval(() => {
                                                                            this.setRemaining();
                                                                        }, 1000);
                                                                    },
                                                                    setRemaining() {
                                                                        const diff = this.expiry - new Date().getTime();
                                                                        this.remaining = parseInt(diff / 1000);
                                                                    },
                                                                    days() {
                                                                        return {
                                                                            value: this.remaining / 86400,
                                                                            remaining: this.remaining % 86400
                                                                        };
                                                                    },
                                                                    hours() {
                                                                        return {
                                                                            value: this.days().remaining / 3600,
                                                                            remaining: this.days().remaining % 3600
                                                                        };
                                                                    },
                                                                    minutes() {
                                                                        return {
                                                                            value: this.hours().remaining / 60,
                                                                            remaining: this.hours().remaining % 60
                                                                        };
                                                                    },
                                                                    seconds() {
                                                                        return {
                                                                            value: this.minutes().remaining,
                                                                        };
                                                                    },
                                                                    format(value) {
                                                                        return ("0" + parseInt(value)).slice(-2)
                                                                    },
                                                                    prelaunchTime() {
                                                                        return {
                                                                            days: this.format(this.days().value),
                                                                            hours: this.format(this.hours().value),
                                                                            minutes: this.format(this.minutes().value),
                                                                            seconds: this.format(this.seconds().value),
                                                                        }
                                                                    },
                                                                }));
                                                            })
                                                        </script>


                                                    </div>
                                                </div>

                                            </div>
                                            <livewire:user.order :product="$product"/>
                                        </div>
                                        <livewire:user.accordion :product="$product"/>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    <div
                        x-show="wishlistPopup"
                        class="fixed inset-0 z-50 overflow-y-auto"
                        role="dialog"
                        aria-modal="true"
                        style="display: none;"
                    >
                        <div class="flex items-center justify-center min-h-screen p-4 text-center">
                            <button
                                aria-label="Close"
                                x-show="wishlistPopup"
                                @click="wishlistPopup = false; noScroll = false;"
                                x-transition:enter="transition ease-out duration-300 transform"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-200 transform"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="fixed inset-0 transition-opacity bg-black bg-opacity-20"
                                style="-webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px);"
                                aria-hidden="true"
                            ></button>

                            <div
                                id="wishlistPopup"
                                x-show="wishlistPopup"
                                x-transition:enter="transition ease-out duration-300 transform"
                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave="transition ease-in duration-200 transform"
                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                class="inline-block w-full max-w-[400px] overflow-hidden text-left transition-all transform bg-white"
                            >
                                <div class="relative">
                                    <button
                                        aria-label="Close"
                                        class="absolute p-2 top-0 right-0 cursor-pointer z-50 "
                                        @click="wishlistPopup = false; noScroll = false;"
                                    >
                                        <svg
                                            aria-hidden="true"
                                            class="icon icon-close text-white"
                                            role="presentation"
                                            style="width:10px; height:16.5px;"
                                            stroke="currentColor"
                                            viewbox="0 0 16.8 16.8"
                                        >
                                            <path
                                                d="M16.8 1.5L15.4.1 8.4 7l-7-7L0 1.4l7 7-7 7 1.4 1.4 7-7 7 7 1.4-1.4-7-7z"
                                                fill="currentColor"></path>
                                        </svg>
                                    </button>
                                    <div class="row">
                                        <div class="relative">
                                            <div
                                                class="grid grid-cols-1 place-content-center text-center text-white uppercase text-base font-bold absolute w-full h-full">
                                                <h1 class="text-[40px]">{{env("APP_NAME")}}</h1>
                                                <span class="mt-4">{{ __("wishlist") }}</span>
                                            </div>
                                            <img
                                                class=""
                                                src="https://sfycdn.speedsize.com/fbaf6506-81e1-43a2-bcc1-80e18c7b0146/https://cdn.shopify.com/s/files/1/0181/2235/files/banner-points.png?crop=center&amp;height=100&amp;v=1652269688&amp;width=400"
                                                loading="lazy"
                                                width=""
                                                height=""
                                            >
                                        </div>
                                        <div class="px-[65px] py-[20px]">

                                            <p class="mb-[10px] font-normal text-[11px] leading-[16.5px] text-center">
                                                {{__("sign:desc")}}
                                            </p>
                                            <div class="mt-[25px] mb-[20px]">
                                                <a
                                                    class="text-[11px] leading-[16.5px] my-[10px] px-[30px] py-[10px] bg-primary-dark text-[#fff] block text-center"
                                                    href="{{route("auth")}}"
                                                >{{ __("sign") }}</a
                                                >
                                                <a
                                                    class="text-[11px] leading-[16.5px] my-[10px] px-[30px] py-[10px] bg-primary-dark text-[#fff] block text-center"
                                                    href="{{route("auth")}}"
                                                >{{ __("register") }}</a
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="shopify-section-template--15863087202513__b9a01309-c064-4327-963c-80627bccee72"
                     class="shopify-section">
                    <div x-data="tagalysGrid" class="tagalys-section my-[20px] [@media(max-width:1023px)]:mt-0"
                         data-product-id="7311779954897">
                        <div class="section-title">
                            <h4 class="text-center font-bold text-black text-[12px] leading-4 uppercase font-display mb-[20px]">
                                {{__("you:like")}}</h4>
                        </div>
                        <livewire:user.show.categories :product="$product"/>

                    </div>

                    <style>
                        .tagalys-section .tab-content-wrapper .tab-content {
                            display: none;
                        }

                        .tagalys-section .tab-content-wrapper .tab-content.active {
                            display: block;
                        }

                        .tagalys-section .tab-header-controls .tab-control {
                            position: relative;
                        }

                        .tagalys-section .tab-header-controls .tab-control.active {
                            font-weight: 700;
                            padding-bottom: 4px;
                        }

                        .tagalys-section .tab-header-controls .tab-control.active:after {
                            content: '';
                            position: absolute;
                            bottom: 0;
                            width: 25px;
                            height: 1px;
                            left: 0;
                            right: 0;
                            margin: auto;
                            background: #000000;
                        }

                        @media only screen and (max-width: 375px) {
                            .tagalys-section .tab-header-controls .tab-control {
                                margin: 0 20px;
                            }
                        }
                    </style>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {

                        });
                    </script>

                </div>
        </main>
    </div>

    <x-slot:ext_footer>
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
        />

        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

        <script>
            const swiper = new Swiper('.swiper', {
                direction: 'horizontal',

                on: {
                    slideChange: function () {
                        document.querySelector(".current-slide").innerHTML = this.activeIndex + 1;
                    },
                },
            });


        </script>
    </x-slot:ext_footer>

</x-layouts.user>
