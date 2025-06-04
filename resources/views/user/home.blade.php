@php use App\Http\Helpers\OrderHelper;use Illuminate\Support\Facades\Storage; @endphp
<x-layouts.user>
    <main
        id="MainContent"
        role="main"
        tabindex="-1"
        class="-mt-[59px]"
        :class="searchOpen ? 'searchIsOpen' : ''">

        @foreach($banners['top'] as $banner)
            <section id="shopify-section-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37"
                     class="shopify-section">
                <div class="mobile-wrapper overflow-hidden md:ml-0 ml-[-1px]" x-data="{ 'showModal': false }">
                    <div
                        class="
      relative pb-10 lg:pb-0 wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37


          h-[calc(100vh-var(--top-bar-height))] h-[calc(100svh-var(--top-bar-height))]





          lg:h-[calc(100vh-var(--top-bar-height))] lg:h-[calc(100svh-var(--top-bar-height))]


    "
                    >


                        <div
                            class="absolute top-0 left-0 w-full h-full object-cover lg:absolute lg:top-0 lg:left-0 lg:w-full lg:h-full lg:object-cover">

                            @if(explode("/",Storage::mimeType($banner->image))[0] == "video")
                                <video playsinline autoplay muted loop class="block object-cover h-full w-full overflow-hidden" >
                                    <source src="{{Storage::url($banner->image)}}">
                                </video>
                            @else
                                <picture class="block object-cover h-full w-full overflow-hidden">
                                    <img
                                        src="{{Storage::url($banner->image)}}"
                                        alt=""
                                        width="1920"
                                        height="2364"
                                        loading="lazy"
                                        class="block object-cover h-full w-full  loading-lazy"
                                    />
                                </picture>
                            @endif



                        </div>


                        <div
                            class="container relative mx-auto left-0 right-0 flex flex-col justify-end py-[79px] px-[30px] absolute top-0 left-0 w-full h-full object-cover lg:absolute lg:top-0 lg:left-0 lg:w-full lg:h-full lg:object-cover z-10">

                            <h3 style="font-size: 20px;font-weight: 500;" class="text-white">{{$banner->subtitle}}</h3>
                            <h2 class="flex flex-col lg:items-center">
                            <span
                                style="color: white"
                                class="text-[1.5rem] lg:text-[2rem] align-mobile align-desktop font-bold uppercase text-mob text-color mb-4 herro-banner-text">{{$banner->title}}</span>

                            </h2>


                            <div class="pb-3">


                                <a href="{{ $banner->link }}"
                                   aria-label="SHOP NOW"
                                   style="color: white !important;"
                                   class="button uppercase text-mob text-color text-white text-sm pb-1 border-b border-mobile border-desktop">
                            <span class="hidden lg:inline-block mr-2">
  <svg
      width="12"
      height="9"
      viewBox="0 0 12 9"
      fill="none"
      xmlns="http://www.w3.org/2000/svg" role="presentation">
    <path
        d="M1 4.74256L10.5879 4.70898"
        stroke="#F8F8F8"
        stroke-width="1.00713"
        stroke-miterlimit="10"/>
    <path
        d="M7.19043 1L10.8128 4.76332"
        stroke="#F8F8F8"
        stroke-width="1.00713"
        stroke-miterlimit="10"/>
    <path
        d="M11.0511 4.29297L7.19043 8.30136"
        stroke="#F8F8F8"
        stroke-width="1.00713"
        stroke-miterlimit="10"/>
  </svg>


</span>
                                    <span class="inline-block lg:hidden mr-2">
  <svg
      width="12"
      height="9"
      viewBox="0 0 12 9"
      fill="none"
      xmlns="http://www.w3.org/2000/svg" role="presentation">
    <path
        d="M1 4.74256L10.5879 4.70898"
        stroke="#F8F8F8"
        stroke-width="1.00713"
        stroke-miterlimit="10"/>
    <path
        d="M7.19043 1L10.8128 4.76332"
        stroke="#F8F8F8"
        stroke-width="1.00713"
        stroke-miterlimit="10"/>
    <path
        d="M11.0511 4.29297L7.19043 8.30136"
        stroke="#F8F8F8"
        stroke-width="1.00713"
        stroke-miterlimit="10"/>
  </svg>


</span>
                                    {{$banner->link_text}}
                                </a>


                            </div>


                        </div>


                    </div>
                </div>
                <style data-shopify>
                    .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .button + .button {
                        margin-left: 20px;
                    }

                    .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .container {
                        height: 100%;
                        align-items: start;
                    }

                    .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .border-mobile {
                        border-bottom: 1px solid #ffffff;
                    }

                    .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .text-mob {
                        color: #ffffff;
                    }

                    .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .align-desktop {
                        text-align: left;
                    }

                    @media (min-width: 768px) {

                        #shopify-section-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 {
                            overflow: hidden;
                        }

                        .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .container {
                            align-items: center;
                        }

                        .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .border-desktop {
                            border-bottom: 1px solid #ffffff;
                        }

                        .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .align-mobile {
                            text-align: left;
                        }
                    }

                    @media (min-width: 1024px) {
                        .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .text-color {
                            color: black;
                        }
                    }
                </style>


            </section>
        @endforeach

        <div id="shopify-section-template--15863086547153__672eafa6-c02d-4c09-a219-e08bd8044a92"
             class="shopify-section">
            <div
                style="--margin_top: 0px;
  --margin_bottom: 30px;
  --margin_top_mob: 0px;
  --margin_bottom_mob: 0px;"
                class="section-template--15863086547153__672eafa6-c02d-4c09-a219-e08bd8044a92-margin | scroll_hor "
            >
                @foreach($products->slice(0,15) as $product)
                    <x-user.product.card :product="$product"/>
                @endforeach

            </div>
            <style>
                .section-template--15863086547153__672eafa6-c02d-4c09-a219-e08bd8044a92-margin {
                    margin-top: var(--margin_top, 0);
                    margin-bottom: var(--margin_bottom, 0);
                }

                .section_template--15863086547153__672eafa6-c02d-4c09-a219-e08bd8044a92-buttons {
                    margin: 0 0 30px 0;
                }

                @media screen and (min-width: 750px) {
                    .section-template--15863086547153__672eafa6-c02d-4c09-a219-e08bd8044a92-margin {
                        margin-top: var(--margin_top_mob, 0);
                        margin-bottom: var(--margin_bottom_mob, 0);
                    }

                    .section_template--15863086547153__672eafa6-c02d-4c09-a219-e08bd8044a92-buttons {
                        margin: 0 0 30px 0;
                    }
                }
            </style>
        </div>

        @foreach($banners['bottom'] as $banner)
            <section id="shopify-section-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37"
                     class="shopify-section">
                <div class="mobile-wrapper overflow-hidden md:ml-0 ml-[-1px]" x-data="{ 'showModal': false }">
                    <div
                        class="
      relative pb-10 lg:pb-0 wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37


          h-[calc(100vh-var(--top-bar-height))] h-[calc(100svh-var(--top-bar-height))]





          lg:h-[calc(100vh-var(--top-bar-height))] lg:h-[calc(100svh-var(--top-bar-height))]


    "
                    >


                        <div
                            class="absolute top-0 left-0 w-full h-full object-cover lg:absolute lg:top-0 lg:left-0 lg:w-full lg:h-full lg:object-cover">


                            @if(explode("/",Storage::mimeType($banner->image))[0] == "video")
                                <video playsinline autoplay muted loop class="block object-cover h-full w-full overflow-hidden" >
                                    <source src="{{Storage::url($banner->image)}}">
                                </video>
                            @else
                                <picture class="block object-cover h-full w-full overflow-hidden">
                                    <img
                                        src="{{Storage::url($banner->image)}}"
                                        alt=""
                                        width="1920"
                                        height="2364"
                                        loading="lazy"
                                        class="block object-cover h-full w-full  loading-lazy"
                                    />
                                </picture>
                            @endif

                        </div>


                        <div
                            class="container relative mx-auto left-0 right-0 flex flex-col justify-end py-[79px] px-[30px] absolute top-0 left-0 w-full h-full object-cover lg:absolute lg:top-0 lg:left-0 lg:w-full lg:h-full lg:object-cover z-10">

                            <h3 style="font-size: 20px;font-weight: 500;" class="text-white">{{$banner->subtitle}}</h3>
                            <h2 class="flex flex-col lg:items-center">
                            <span
                                style="color: white"
                                class="text-[1.5rem] lg:text-[2rem] align-mobile align-desktop font-bold uppercase text-mob text-color mb-4 herro-banner-text">{{$banner->title}}</span>

                            </h2>


                            <div class="pb-3">


                                <a href="{{ $banner->link }}"
                                   aria-label="SHOP NOW"
                                   style="color: white !important;"
                                   class="button uppercase text-mob text-color text-white text-sm pb-1 border-b border-mobile border-desktop">
                            <span class="hidden lg:inline-block mr-2">
  <svg
      width="12"
      height="9"
      viewBox="0 0 12 9"
      fill="none"
      xmlns="http://www.w3.org/2000/svg" role="presentation">
    <path
        d="M1 4.74256L10.5879 4.70898"
        stroke="#F8F8F8"
        stroke-width="1.00713"
        stroke-miterlimit="10"/>
    <path
        d="M7.19043 1L10.8128 4.76332"
        stroke="#F8F8F8"
        stroke-width="1.00713"
        stroke-miterlimit="10"/>
    <path
        d="M11.0511 4.29297L7.19043 8.30136"
        stroke="#F8F8F8"
        stroke-width="1.00713"
        stroke-miterlimit="10"/>
  </svg>


</span>
                                    <span class="inline-block lg:hidden mr-2">
  <svg
      width="12"
      height="9"
      viewBox="0 0 12 9"
      fill="none"
      xmlns="http://www.w3.org/2000/svg" role="presentation">
    <path
        d="M1 4.74256L10.5879 4.70898"
        stroke="#F8F8F8"
        stroke-width="1.00713"
        stroke-miterlimit="10"/>
    <path
        d="M7.19043 1L10.8128 4.76332"
        stroke="#F8F8F8"
        stroke-width="1.00713"
        stroke-miterlimit="10"/>
    <path
        d="M11.0511 4.29297L7.19043 8.30136"
        stroke="#F8F8F8"
        stroke-width="1.00713"
        stroke-miterlimit="10"/>
  </svg>


</span>
                                    {{$banner->link_text}}
                                </a>


                            </div>


                        </div>


                    </div>
                </div>
                <style data-shopify>
                    .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .button + .button {
                        margin-left: 20px;
                    }

                    .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .container {
                        height: 100%;
                        align-items: start;
                    }

                    .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .border-mobile {
                        border-bottom: 1px solid #ffffff;
                    }

                    .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .text-mob {
                        color: #ffffff;
                    }

                    .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .align-desktop {
                        text-align: left;
                    }

                    @media (min-width: 768px) {

                        #shopify-section-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 {
                            overflow: hidden;
                        }

                        .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .container {
                            align-items: center;
                        }

                        .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .border-desktop {
                            border-bottom: 1px solid #ffffff;
                        }

                        .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .align-mobile {
                            text-align: left;
                        }
                    }

                    @media (min-width: 1024px) {
                        .wrapper-template--15863086547153__cae81768-44de-421e-9b91-e5bd18870f37 .text-color {
                            color: black;
                        }
                    }
                </style>


            </section>
        @endforeach


        <div
            id="shopify-section-template--shopify-section-template--15863086547153__30b16562-6f7f-4429-9541-8c5025cd1ca4-c02d-4c09-a219-e08bd8044a92"
            class="shopify-section">
            <div
                style="--margin_top: 0px;
  --margin_bottom: 30px;
  --margin_top_mob: 0px;
  --margin_bottom_mob: 0px;"
                class="section-template--15863086547153__672eafa6-c02d-4c09-a219-e08bd8044a92-margin | scroll_hor "
            >
                @foreach($products->slice(15,30) as $product)
                    <x-user.product.card :product="$product"/>
                @endforeach

            </div>
            <style>
                .section-template--15863086547153__672eafa6-c02d-4c09-a219-e08bd8044a92-margin {
                    margin-top: var(--margin_top, 0);
                    margin-bottom: var(--margin_bottom, 0);
                }

                .section_template--15863086547153__672eafa6-c02d-4c09-a219-e08bd8044a92-buttons {
                    margin: 0 0 30px 0;
                }

                @media screen and (min-width: 750px) {
                    .section-template--15863086547153__672eafa6-c02d-4c09-a219-e08bd8044a92-margin {
                        margin-top: var(--margin_top_mob, 0);
                        margin-bottom: var(--margin_bottom_mob, 0);
                    }

                    .section_template--15863086547153__672eafa6-c02d-4c09-a219-e08bd8044a92-buttons {
                        margin: 0 0 30px 0;
                    }
                }
            </style>
        </div>

        <noscript class="endOfLayoutContentX" type="text/mark"></noscript>

    </main>
</x-layouts.user>
