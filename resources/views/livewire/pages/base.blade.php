@php use App\Models\Settings;use Illuminate\Support\Facades\Storage; @endphp
@props([
    "obj"
])
<div>
    <main id="MainContent" role="main" tabindex="-1" :class="searchOpen ? 'searchIsOpen' : ''" class=""><h1
            role="heading" aria-level="1" data-uw-rm-heading="h1"
            style="clip: rect(1px, 1px, 1px, 1px)!important;height:1px!important;width:1px!important;overflow:hidden!important;position:absolute!important;top:0!important;left:0!important;z-index:-1!important;opacity:0!important"
            id="userway-h1-heading" data-uw-rm-ignore="">About | REPRESENT CLO</h1>
        <div id="shopify-section-template--15919045837009__main" class="shopify-section">
            <div class="section-template--15919045837009__main" style="--section-margin-top: 50px;
                  --section-margin-bottom: 80px;
                  --section-margin-top-mob: 30px;
                  --section-margin-bottom-mob: 50px;">
                <div
                    class="max-w-[57.875rem] w-full text-left md:text-center pl-[1rem] pr-[1rem] md:px-0 mb-[1.625rem] md:mb-[2.438rem] mx-auto | rte | text-default text-black">

                    <div class="page_content">
                        <meta charset="utf-8">
                        <div style="text-align: center;">
                            <span style="font-weight: 400;">
                               Alibimenstyle.uz
                            </span>
                        </div>
                    </div>

                </div>

                <div class="about-timeline mx-auto max-w-[57.875rem] w-full">
                    <div x-data="
        {
          tab: '997167e0-c944-4070-bfe5-28912459a55e'
        }
      ">

                        <div role="region">

                            <div id="tabpanel-997167e0-c944-4070-bfe5-28912459a55e" role="tabpanel"
                                 aria-labelledby="tab-1" x-show="tab === '997167e0-c944-4070-bfe5-28912459a55e'"
                                 style="display: none;">
                                <div class="flex flex-col md:flex-row md:items-center">
                                    <div class="md:max-w-[21.563rem] w-full px-[1rem]">
                                        
                                        <h3 class="font-global_weight text-[1rem] mb-[1.25rem] md:hidden">{!! strip_tags(Settings::get("about:title")) !!}</h3>

                                        <picture class="block object-cover h-full w-full overflow-hidden">
                                            <img
                                                src="{{Storage::url($obj->image)}}"
                                                alt="stage" width="720" height="720" loading="lazy"
                                                class="block object-cover h-full w-full  loading-lazy"
                                                data-uw-rm-alt-original="" data-uw-rm-alt="BE">
                                        </picture>


                                    </div>
                                    <div class="md:max-w-[29.813rem] md:ml-[3.125rem] mt-[1.5rem] md:mt-0">
                                        <h3 class="font-global_weight text-[1.5rem] mb-[1.25rem] hidden md:block">The
                                            {{$obj->title}}</h3>
                                        <div class="text-[1rem] pl-4 pr-[45px] md:p-0 | rte | text-default text-black">
                                            {!! $obj->content !!}
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    .year.active {
                        color: white;
                        background-color: var(--primary-dark);
                    }

                    .about-timeline .swiper-slide {
                        width: auto;
                    }

                    .page-timeline .herro-banner-text {
                        margin-bottom: 80px
                    }

                    .section-template--15919045837009__main {
                        margin-top: var(--section-margin-top);
                        margin-bottom: var(--section-margin-bottom);

                    }

                    @media screen and (max-width: 639px) {
                        .about-timeline .swiper-slide {
                            font-size: 14px;
                        }
                    }

                    @media screen and (max-width: 1023px) {
                        .section-template--15919045837009__main {
                            margin-top: var(--section-margin-top-mob);
                            margin-bottom: var(--section-margin-bottom-mob);
                        }

                    }

                    .page_content div:last-child {
                        margin-top: 10px;
                    }
                </style>


            </div>

            <style>
                .section-template--15919045837009__4b8891a9-6d29-446e-b336-4107be6be162 {
                    margin-top: var(--section-margin-top);
                    margin-bottom: var(--section-margin-bottom);

                }

                @media screen and (max-width: 1023px) {
                    .section-template--15919045837009__4b8891a9-6d29-446e-b336-4107be6be162 {
                        margin-top: var(--section-margin-top-mob);
                        margin-bottom: var(--section-margin-bottom-mob);
                    }
                }
            </style>


        </div>

    </main>
</div>
