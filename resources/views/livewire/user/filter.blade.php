<div>

    <div x-cloak :class="filterModal.open ? '!visible' : ''" class="invisible fixed w-[100vw] h-[100vh] z-[999999999999] flex justify-end !backdrop-blur-sm">
        <div :class="filterModal.open ? '!mr-[0]' : ''" style="transition: all 0.4s ease-in-out" class="bg-white h-[100vh] mr-[-400px] w-[calc(400px)] p-5">

            <div class="flex flex-row items-center justify-between">
                <p class="text-[16px] font-global_weight">Filter</p>
                <button aria-label="Close Filter" class="filters-close cursor-pointer | p-4 absolute top-0 right-0" x-on:click="filterModal.toggle()">
                    <svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg" role="presentation">
                        <line x1="1.35355" y1="0.445275" x2="14.0815" y2="13.1732" stroke="black"></line>
                        <line x1="1.01656" y1="13.3359" x2="13.7445" y2="0.607978" stroke="black"></line>
                    </svg>
                </button>
            </div>
            <div class="mb-6">
                <div class="mt-5"></div>

                <div class="flex flex-col">
                    <p class="text-[14px] leading-[150%] my-5 heading" style="color:#666666;">Sort
                        by</p>

                    <template x-for="filter in filterModal.sort">
                        <button class="mt-2 sort-filter storefront_filter_ajax_trigger" x-on:click="filterModal.filter.sort=filter.field;filterModal.filter.direct=filter.direct;filterModal.filter.name=filter.name">
                            <div :class="filterModal.filter.name === filter.name ? 'active' : ''" class="sort-filter flex storefront_filter_ajax_trigger">
                                <div class="facets-checkbox">
                                    <svg width="12" height="9" viewBox="0 0 12 9" fill="none" xmlns="http://www.w3.org/2000/svg" role="presentation">
                                        <path d="M1 4.5L4.75 8L11 1" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </div>
                                <span class="ml-3 text-[12px]" x-text="filter.name"></span>
                            </div>
                        </button>
                    </template>

                    <hr class="block my-4 hr-f" role="presentation" data-uw-rm-sr="">
                </div>

                <div class="sizeFacet">
                    <div id="filters-sizes">
                        <div id="sizeFacet_tops_bottoms">
                            <p class="text-[14px] leading-[150%] my-5 heading size filter-list-heading" style="color: rgb(102, 102, 102); display: block;">{{__("sizes")}}</p>


                            <ul class="grid grid-cols-5 filter-list">

                                @foreach($sizes as $size)
                                <li :class="filterModal.filter.sizes.includes({{$size->id}}) ? 'outline-black' : ''" x-on:click="filterModal.select({{$size->id}})" class="cursor-pointer text-center outline outline-1 outline-[#CACACA] md:hover:outline-black md:hover:z-10" style="display: block;">
                                    <a class="storefront_filter_ajax_trigger py-4 w-full h-full block text-[12px] size-filter ">
                                        {{$size->name}}
                                    </a>
                                </li>
                                @endforeach

                            </ul>
                        </div>

                        <hr class="block my-4 hr-f" role="presentation" data-uw-rm-sr="">
                    </div>
                </div>


            </div>
            <div class="grid grid-cols-2 gap-0 filterBottom bg-white | mt-auto |  mx--4 relative z-20">
                <div x-on:click="filterModal.clear();filterModal.open=false;$dispatch('setFilter',{sort:filterModal.filter.sort,sizes:filterModal.filter.sizes,direction:filterModal.filter.direct})" class="font-global_weight uppercase text-xs text-black bg-white justify-center items-center h-[38px] flex cursor-pointer clear-filters" id="clear-refinements-btn">
                    CLEAR SELECTION
                </div>
                <div class="font-global_weight uppercase text-xs text-white bg-primary-dark justify-center items-center h-[38px] flex cursor-pointer filters-close" x-on:click="filterModal.open=false;$dispatch('setFilter',{sort:filterModal.filter.sort,sizes:filterModal.filter.sizes,direction:filterModal.filter.direct})">
                    <div class="pointer-events-none">
                        <span>Apply</span>
                    </div>
                </div>
            </div>



        </div>
    </div>


    <script>
        window.addEventListener("alpine:init", () => {
            Alpine.data("vars", () => ({
                filterModal: {
                    open: false,

                    toggle() {
                        this.open = !this.open
                    },
                    filter: {
                        sort: "id",
                        sizes: [],
                        direct: "desc",
                        name: `{!! __("created:at:up") !!}`
                    },
                    sort: [{
                            name: `{!! __("created:at:up") !!}`,
                            field: 'id',
                            direct: 'desc'
                        },
                        {
                            name: `{!! __("created:at:down") !!}`,
                            field: 'id',
                            direct: 'asc'
                        },
                        {
                            name: `{{__("price:up")}}`,
                            field: 'price',
                            direct: 'desc'
                        },
                        {
                            name: `{{__("price:down")}}`,
                            field: 'price',
                            direct: 'asc'
                        }
                    ],
                    clear() {
                        this.filter.sizes = [];
                    },

                    select(size) {
                        if (this.filter.sizes.includes(size)) {
                            let index = this.filter.sizes.indexOf(size);
                            this.filter.sizes.splice(index, 1)
                        } else {
                            this.filter.sizes.push(size)
                        }
                    },


                },

            }))
        })
    </script>
</div>