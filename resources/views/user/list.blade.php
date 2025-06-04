@php @endphp
<x-layouts.user>
    <x-slot:top>
        <livewire:user.filter/>
    </x-slot:top>

    <div>
        <main id="MainContent" role="main" tabindex="-1" :class="searchOpen ? 'searchIsOpen' : ''" class=""><h1
                role="heading" aria-level="1" data-uw-rm-heading="h1"
                style="clip: rect(1px, 1px, 1px, 1px)!important;height:1px!important;width:1px!important;overflow:hidden!important;position:absolute!important;top:0!important;left:0!important;z-index:-1!important;opacity:0!important"
                id="userway-h1-heading" data-uw-rm-ignore="">Luxury Apparel &amp; Streetwear | REPRESENT CLO</h1>
            <section id="shopify-section-template--15863085990097__main" class="shopify-section">
                <div x-data="collection_native">
                    <div x-data="{showMore: false}" class="w-full mx-auto mt-2 px-6">


                        <div class="text-[13px] text-black text-left mt-1 md:mt-4 desc-mob">
                            <div class="collection-info max-w-[1000px] mb-4 | text-default text-black">
                                <div class="hidden lg:block">

                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="collection-main bg-white">

                        <div id="autocomplete2" class="hidden"></div>
                        <div
                            class="filter-sticky flex flex-row justify-between items-center md:mb-4 mb-1 px-6 flex-wrap">

                            <div class="storefront-filters-type-top max-w-[calc(100%-100px)] ">
                                <div class="shopify-storefront-filter product-type-filter">
                                    <ul class="flex flex-row py-[2px] md:gap-[50px] gap-[2rem]">
                                        @if($type == "subcategory" or \App\Models\Category::findOrField($id)->subcategory->count() >= 1)
                                            @foreach(\App\Models\SubCategory::findOrField($id)->category()->first()->subcategory()->orderBy('position')->get() as $category)
                                                <li class="all-filter" style="display: block;">
                                                    <a href="{{route("category",['type'=>"subcategory","id"=>$category->id])}}"
                                                       class="pointer  relative storefront_filter_ajax_trigger filter-link category-filter active clear-filters"
                                                       data-value="all">
                                                    <span
                                                        class="@if($type == "subcategory" and $id == $category->id) font-bold @endif text-[12px] uppercase text-primary-gray pointer-events-none">{{$category->name}}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        @else
                                            @foreach($categories as $category)
                                                <li class="all-filter" style="display: block;">
                                                    <a href="{{route("category",['type'=>"category","id"=>$category->id])}}"
                                                       class="pointer relative storefront_filter_ajax_trigger filter-link category-filter active clear-filters"
                                                       data-value="all">
                                                    <span
                                                        class="@if($type == "category" and $id == $category->id) font-bold @endif  text-[12px] uppercase text-primary-gray pointer-events-none">{{$category->name}}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        @endif

                                    </ul>
                                </div>
                            </div>

                            <button x-on:click="filterModal.toggle()" aria-label="Refine"
                                    id="filter-trigger-shopify-storefront"
                                    class="filter-trigger flex gap-3 items-center flex-row p-4 pr-0 cursor-pointer">
                                <span class="pt-filter-count hidden">0</span>
                                <span class="text-[12px] uppercase">{{__("filter")}}</span>
                                <svg width="16" height="14" viewBox="0 0 16 14" fill="none"
                                     xmlns="http://www.w3.org/2000/svg" role="presentation">
                                    <g clip-path="url(#clip0_2206_8497)">
                                        <path d="M-0.000976562 9.44746V10.5146H8.03181V9.44746H-0.000976562Z"
                                              fill="black"></path>
                                        <path d="M12.2676 9.44746V10.5146H15.1364V9.44746H12.2676Z" fill="black"></path>
                                        <path
                                            d="M10.1214 7.82778C10.5342 7.82767 10.9379 7.95269 11.2813 8.18705C11.6246 8.4214 11.8922 8.75456 12.0503 9.14438C12.2084 9.53419 12.2497 9.96316 12.1692 10.377C12.0887 10.7909 11.8899 11.171 11.5979 11.4694C11.306 11.7678 10.934 11.971 10.529 12.0532C10.124 12.1355 9.7043 12.0932 9.32285 11.9317C8.94141 11.7702 8.61541 11.4967 8.38609 11.1458C8.15677 10.7949 8.03443 10.3824 8.03455 9.96041C8.03515 9.39499 8.25521 8.85291 8.64643 8.4531C9.03765 8.05329 9.56808 7.8284 10.1214 7.82778ZM10.1214 6.80164C9.50986 6.80152 8.91208 6.98673 8.4036 7.33384C7.89512 7.68095 7.49879 8.17437 7.26473 8.75169C7.03067 9.32901 6.96941 9.9643 7.08867 10.5772C7.20794 11.1901 7.50239 11.7531 7.93477 12.195C8.36716 12.6369 8.91806 12.9378 9.5178 13.0597C10.1175 13.1816 10.7392 13.1189 11.3041 12.8797C11.869 12.6405 12.3518 12.2355 12.6915 11.7159C13.0311 11.1962 13.2124 10.5853 13.2123 9.96041C13.2123 9.12265 12.8866 8.3192 12.3069 7.72682C11.7273 7.13443 10.9411 6.80164 10.1214 6.80164Z"
                                            fill="black"></path>
                                        <path d="M7.10461 2.6457V3.71289H15.1374V2.6457H7.10461Z" fill="black"></path>
                                        <path d="M-0.000854492 2.64668L-0.000854492 3.71387H2.868V2.64668H-0.000854492Z"
                                              fill="black"></path>
                                        <path
                                            d="M5.01382 1.02615C5.42671 1.02603 5.83036 1.15106 6.17372 1.38541C6.51708 1.61977 6.78471 1.95292 6.94277 2.34274C7.10083 2.73256 7.14221 3.16152 7.06169 3.57538C6.98116 3.98924 6.78235 4.36939 6.49039 4.66776C6.19843 4.96614 5.82644 5.16932 5.42148 5.25161C5.01651 5.3339 4.59676 5.2916 4.21532 5.13007C3.83388 4.96854 3.50788 4.69503 3.27856 4.34414C3.04924 3.99325 2.9269 3.58073 2.92701 3.15877C2.92762 2.59335 3.14768 2.05127 3.5389 1.65146C3.93012 1.25165 4.46055 1.02677 5.01382 1.02615ZM5.01382 8.7099e-07C4.40226 -0.000463102 3.8043 0.18445 3.2956 0.531349C2.78689 0.878247 2.3903 1.37154 2.15598 1.94883C1.92165 2.52612 1.86014 3.16147 1.97921 3.77449C2.09828 4.38752 2.39259 4.95068 2.82491 5.39274C3.25723 5.83479 3.80812 6.13588 4.40791 6.25791C5.0077 6.37993 5.62943 6.31742 6.19445 6.07828C6.75947 5.83913 7.24238 5.4341 7.58211 4.91442C7.92184 4.39474 8.10311 3.78376 8.103 3.15877C8.103 2.32101 7.77735 1.51757 7.1977 0.925183C6.61804 0.332799 5.83186 8.7099e-07 5.0121 8.7099e-07H5.01382Z"
                                            fill="black"></path>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_2206_8497">
                                            <rect width="15.1378" height="13.1194" fill="white"></rect>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </button>
                        </div>
                        <h2 class="sr-only">Products in 'Discover All Products' collection:</h2>


                        <livewire:user.products :type="$type" :id="$id"/>


                    </div>
                </div>
            </section><!-- End of layout -->
            <noscript class="endOfLayoutContentX" type="text/mark"></noscript>
        </main>
    </div>

</x-layouts.user>

