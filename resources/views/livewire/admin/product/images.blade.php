@php use App\Services\Livewire\ProductImagesService;use Illuminate\Support\Facades\Storage; @endphp
@props([
    "colors",
    "images",
    "allColors"
])
<div x-data="data" x-on:livewire-upload-finish="console.log('salom');$dispatch('uploaded')">


    <x-slot:top>
        <div id="alert-container"></div>
        <div x-cloak x-show="modal.open"
             class="z-[99999] flex justify-center items-center fixed w-[100vw] h-[100vh] backdrop-blur-sm backdrop-brightness-50">
            <div
                class="modal-container relative flex justify-between flex-col p-3 rounded-3 bg-white w-[90%] max-w-[500px] min-h-[300px]">
                <div class="header flex justify-between">
                    <p class="">{{ __("product:image:modal") }}</p>
                    <i class="fas fa-window-close fs-6 cursor-pointer" x-on:click="modal.toggle"></i>
                </div>
                <div class="">

                    <label class="form-label"
                           for="product-subcategory">{{__("product.color")}}
                    </label>
                    <select x-model="model.color" required class="form-select"
                            id="product-subcategory"
                            name="gender">
                        <option value="none">---------</option>
                        @foreach($allColors as $color)
                            <option value="{{$color->id}}">{{ $color->name }}</option>
                        @endforeach

                    </select>
                </div>
                <div class="bottom-3 flex justify-between w-full">
                    <button class="btn btn-danger"
                            x-on:click="$dispatch('delete',{index:image.id});modal.open = false">{{ __("delete") }}</button>
                    <button class="btn btn-primary"
                            x-on:click="$dispatch('setColor',{index:image.id,color:model.color});modal.open=false"
                    >{{ __("save") }}</button>
                </div>
            </div>
        </div>
    </x-slot:top>


    <div class="card">
        <div class="card-header border-bottom border-solid grid grid-cols-4">
            <p class="inline sm:col-span-3 col-span-4">{{__("product.images")}}</p>
            <button wire:click="submit" class="btn sm:col-span-1 col-span-4 btn-primary">{{ __("save") }}</button>
        </div>

        <div class="p-4">
            <label for="images" class="w-full">
                <div class="tab-pane preview-tab-pane active" role="tabpanel"
                     aria-labelledby="tab-dom-2ad5f821-cce1-4c15-803c-4f17505047da"
                     id="dom-2ad5f821-cce1-4c15-803c-4f17505047da">
                    <div class="dropzone dropzone-single p-0 dz-clickable">
                        <div class="dz-preview dz-preview-single"></div>
                        <div class="dz-message" data-dz-message="data-dz-message">
                            <div class="dz-message-text">
                                <img class="me-2"
                                     src="{{asset("assets/img/icons/cloud-upload.svg")}}"
                                     width="25" alt="">{{__("all:images")}}
                            </div>
                        </div>
                    </div>
                </div>
            </label>
            <input wire:model="image" class="hidden" type="file" id="images" multiple
                   accept="image/jpeg,image/jpg,image/png">

        </div>
        <div class="card-body grid lg:grid-cols-4 md:grid-cols-3 gap-3 grid-cols-2">

            @foreach($images as $index=>$image)
                @php
                    $filterImages = (new ProductImagesService)->filter($colors,$image['path']);
                @endphp
                <div class="image relative cursor-pointer" x-on:click="setImage({{$index}})">
                    <div class="absolute flex justify-between z-[99] w-full h-full">
                        @if(isset($image['color']))
                            <p class="text-white font-bold text-[20px] line-clamp-2 p-1">{{ $image['color']['name'] }}</p>
                        @elseif($filterImages->count() >= 1)
                            <p class="text-white font-bold text-[20px] line-clamp-2 p-1">{{ $filterImages->first()->color->name }}</p>
                        @else
                            <div></div>
                        @endif
                    </div>
                    <img class="brightness-[0.7] w-full object-fit-cover h-[150px]"
                         src="{{($image["status"] ?? "old") != "new" ? Storage::url($image['path']) : $image['path']->temporaryUrl()}}"
                         alt="">
                </div>
            @endforeach
        </div>

    </div>

    @vite(['resources/js/app.js',"resources/js/product/images.js"])
</div>
