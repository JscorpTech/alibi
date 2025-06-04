@php use Illuminate\Support\Facades\Storage; @endphp
<div>
    <div
        style="transition: all .3s ease-in-out"
        :class="modal.open ? '!visible backdrop-blur-sm' : ''"
        class="invisible z-[999989] !h-[100%] flex justify-center  overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div style="transition: all .3s ease-in-out" :class="modal.open ? 'mr-[0px]' : ''"
             class="transition ease-in-out delay-150 mr-[-350px] fixed z-[99999999] backdrop-blur-md inset-y-0 right-0 max-w-full flex drawer"
             data-uw-cer-popup-wrapper="">
            <div id="slideCart" class="w-screxen max-w-[337px] md:max-w-[437px] bg-white">
                <div class="relative h-[calc(100%-50px)] flex flex-col overflow-y-scroll item-list"
                     x-ref="cart_content">

                    <form method="post" class="relative" data-count="1">
                        <div class="flex justify-between py-[1.188rem] px-6 bg-white sticky top-0 z-20">
                            <h3 :data-count="item_count" class="text-xs font-global_weight uppercase"
                                x-text="item_count + ' items in Your cart'" data-count="1">1 items in Your cart</h3>
                            <button aria-label="Remove" type="button" @click="
                                      modal.open = false;
                                    " class="rounded-md scale-75 | absolute top-0  right-0 p-2">

                                <svg width="18" height="17" viewBox="0 0 18 17" fill="none"
                                     xmlns="http://www.w3.org/2000/svg" role="presentation">
                                    <line y1="-0.5" x2="22.6267" y2="-0.5"
                                          transform="matrix(0.70713 -0.707084 0.70713 0.707084 1 17)"
                                          stroke="black"></line>
                                    <line y1="-0.5" x2="22.6267" y2="-0.5"
                                          transform="matrix(0.70713 0.707084 -0.70713 0.707084 1 1)"
                                          stroke="black"></line>
                                </svg>


                            </button>
                        </div>


                        <div class="relative h-full ">
                            <img class="max-w-[400px] object-fit-cover max-w-[100%]" src="{{Storage::url($product->sizeImage?->image_1)}}" alt="">
                            <img class="max-w-[400px] object-fit-cover max-w-[100%]" src="{{Storage::url($product->sizeImage?->image_2)}}" alt="">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
