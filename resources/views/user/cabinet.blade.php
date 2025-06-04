<x-layouts.user>
    <div x-data="{
        activeTab:'{{ __("Orders") }}',

        pages:[
            '{{ __("Profile") }}',
            '{{ __("Orders") }}',
            '{{ __("Wishlist") }}'
        ]
    }">
        <main id="MainContent" role="main" tabindex="-1" :class="searchOpen ? 'searchIsOpen' : ''"
              style="transition: all 0s ease 0s;">
            <h1 role="heading" aria-level="1" data-uw-rm-heading="h1"
                style="clip: rect(1px, 1px, 1px, 1px)!important;height:1px!important;width:1px!important;overflow:hidden!important;position:absolute!important;top:0!important;left:0!important;z-index:-1!important;opacity:0!important"
                id="userway-h1-heading" data-uw-rm-ignore="">Account | REPRESENT
                CLO</h1>
            <div class="md:pt-20">
                <ul class="filter-sticky flex flex-row px-3.5 justify-start md:justify-center divide-x mb-7 overflow-auto uppercase">
                    <template x-for="page in pages">
                        <li x-on:click="activeTab = page"
                            class="cursor-pointer whitespace-nowrap text-[12px] p-[20px] leading-[14px] text-primary-gray"
                            :class="activeTab !== page || 'bg-[#F2F2F2] !text-black'">
                            <p x-text="page"></p>
                        </li>

                    </template>

                    <a href="{{route("logout")}}">
                        <li class="cursor-pointer whitespace-nowrap text-[12px] p-[20px] leading-[14px] text-primary-gray"
                            :class="activeTab !== page || 'bg-[#F2F2F2] !text-black'">
                            <p>{{ __("Logout") }}</p>
                        </li>
                    </a>

                </ul>


                <div class="container m-auto pt-14 md:pt-8 mb-12">
                    <div x-show="activeTab === '{{ __("Orders")}}'" class="max-w-[960px] m-auto">
                        <div>
                            <div class="grid-cols-5 px-3.5 md:px-4 flex">
                                <div class="text-xs w-[200px] font-global_weight px-2">{{ __("order.number") }}</div>
                                <div
                                        class="text-xs w-[200px] font-global_weight px-2 text-center">{{ __("order.date") }}</div>
                                <div
                                        class="text-xs w-[200px] font-global_weight px-2 text-center">{{ __("status") }}</div>
                                <div
                                        class="text-xs w-[200px] font-global_weight px-2 text-center">{{ __("total") }}</div>
                                <div
                                        class="text-xs w-[200px] font-global_weight px-2 text-center">{{ __("price") }}</div>
                                <div
                                        class="text-xs w-[200px] font-global_weight px-10 text-center">{{ __("product") }}</div>
                            </div>
                            <hr class="mx-4 mt-3.5 mb-5" role="presentation" data-uw-rm-sr="">

                            @foreach($orders as $orders_list)
                                @foreach($orders_list->orders as $order)
                                    <div class="mt-7">
                                    <div class="grid-cols-5 px-3.5 md:px-4 flex">
                                        <div class="text-xs w-[200px] font-global_weight px-2">#{{$order->id}}</div>
                                        <div
                                                class="text-xs w-[200px] font-global_weight px-2 text-center">{{$order->created_at->format('d.m.Y H:i')}}</div>
                                        <div
                                                class="text-xs w-[200px] font-global_weight px-2 text-center">{{__($order->status)}}</div>
                                        <div
                                                class="text-xs w-[200px] font-global_weight px-2 text-center">{{$order->count}}</div>
                                        <div
                                                class="text-xs w-[200px] font-global_weight px-2 text-center">{{ number_format($order->price) }} {{ __("currency") }}</div>
                                        <div class="text-xs w-[200px] font-global_weight px-10 text-center"><a
                                                    href="{{route("show",$order->product->id ?? "#")}}">#{{$order->product->id ?? "None"}}</a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    <div x-show="activeTab === '{{ __("Profile")}}'" class="max-w-[960px] m-auto">
                        <div :class="activeTab !== 'orders' || 'block md:hidden' "
                             style="letter-spacing:0.133333px;padding-left:13.125px;padding-right:13.125px;max-width:472px;margin-top:61px;margin: auto;box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">
                            <p style="letter-spacing:0.133333px;line-height:16px;font-size:16px;margin:0px;box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">{{$user->full_name}}</p>
                            <div
                                    style="letter-spacing:0.133333px;justify-content:space-between;flex-direction:row;display:flex;margin-top:15px;box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">
                                <p style="letter-spacing:0.133333px;line-height:16px;font-size:16px;margin:0px;box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">{{$user->phone}}</p>
                                <a href="{{route("logout")}}"
                                   style="text-decoration-line:underline;line-height:16px;font-size:12px;color:rgb(0, 0, 0);box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">LOGOUT</a>
                            </div>
                            <div
                                    style="margin-top: 20px !important;letter-spacing:0.133333px;justify-content:space-between;flex-direction:row;display:flex;margin-top:15px;box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">
                                <p style="letter-spacing:0.133333px;line-height:16px;font-size:16px;margin:0px;box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">{{ __("card") }}</p>
                                <p style="font-size:20px !important;text-decoration-line:underline;line-height:16px;font-size:12px;color:rgb(0, 0, 0);box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">{{ __($user->card) }}</p>
                            </div>
                            <div
                                    style="margin-top: 20px !important;letter-spacing:0.133333px;justify-content:space-between;flex-direction:row;display:flex;margin-top:15px;box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">
                                <p style="letter-spacing:0.133333px;line-height:16px;font-size:16px;margin:0px;box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">{{ __("cashback") }}</p>
                                <p style="font-size:20px !important;text-decoration-line:underline;line-height:16px;font-size:12px;color:rgb(0, 0, 0);box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">{{ number_format($user->balance) }} {{ __("currency") }}</p>
                            </div>
                            <div
                                    style="letter-spacing:0.133333px;margin-bottom:26.25px;margin-top:22.5px;box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">
                                <a href="{{ route("home") }}"
                                   style="--btn_epic_height: 36px;position:relative;background:rgb(13, 14, 14) none repeat scroll 0% 0% / auto padding-box border-box;transform:matrix(1, 0, 0, 1, 0, 0);text-decoration:none solid rgb(255, 255, 255);transition-delay:0.6s;overflow:hidden;border-style:solid;border-width:0.8px;cursor:pointer;display:inline-block;height:36px;line-height:36px;padding:0px;user-select:none;--btn_epic_color_01: #0d0e0e;--btn_epic_color_02: #ffffff;border-color:rgb(13, 14, 14);color:rgb(255, 255, 255);text-transform:uppercase;font-size:11.25px;padding-left:0px;padding-right:0px;padding-top:0px;padding-bottom:0px;box-sizing:border-box;background-color:rgb(13, 14, 14);">
                                    <div
                                            style="position:relative;top:0px;width: 100%;height:36px;text-transform:uppercase;overflow:hidden;letter-spacing:0.133333px;box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">

                                        <span
                                                style="color:rgb(255, 255, 255);position:relative;transform:matrix(1, 0, 0, 1, 0, 0);z-index:1;top:0px;width: 100%;text-align:center;transition:transform 0.5s ease 0s;left:0px;padding:0px 15px;display:block;letter-spacing:0.133333px;box-sizing:border-box;border-width:0px;border-style:solid;border-color:rgb(229, 231, 235);">{{ __("home") }}</span>
                                    </div>
                                </a>
                            </div>
                        </div>


                    </div>

                    <div x-show="activeTab === '{{ __("Wishlist") }}'" class="max-w-[960px] m-auto">
                        <div>
                            <div class="grid-cols-5 px-3.5 md:px-4 flex">
                                <div class="text-xs w-[200px] font-global_weight px-2">{{__("product:id")}}</div>
                                <div
                                        class="text-xs w-[200px] font-global_weight px-10 text-center">{{__("product:name")}}</div>
                                <div
                                        class="text-xs w-[200px] font-global_weight px-2 text-center">{{__("product:count")}}</div>
                                <div
                                        class="text-xs w-[200px] font-global_weight px-2 text-center">{{__("product:status")}}</div>
                                <div
                                        class="text-xs w-[200px] font-global_weight px-2 text-center">{{__("product:gender")}}</div>
                            </div>
                            <hr class="mx-4 mt-3.5 mb-5" role="presentation" data-uw-rm-sr="">

                            @foreach($basket as $order)
                                <div class="mt-7">
                                    <div class="grid-cols-5 px-3.5 md:px-4 flex">
                                        <div class="text-xs w-[200px] font-global_weight px-2"><a
                                                    href="{{route("show",$order->id)}}">#{{$order->id}}</a></div>
                                        <div
                                                class="text-xs w-[200px] font-global_weight px-2 text-center">{{$order->name}}</div>
                                        <div
                                                class="text-xs w-[200px] font-global_weight px-2 text-center">{{$order->count()}}</div>
                                        <div
                                                class="text-xs w-[200px] font-global_weight px-2 text-center">{{__($order->status)}}</div>
                                        <div
                                                class="text-xs w-[200px] font-global_weight px-10 text-center">{{__($order->gender)}}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div><!-- End of layout -->
            <noscript class="endOfLayoutContentX" type="text/mark"></noscript>

        </main>
    </div>
</x-layouts.user>
