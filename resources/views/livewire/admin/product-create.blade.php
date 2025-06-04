@php use App\Enums\GenderEnum;use App\Enums\ProductStatusEnum;use App\Http\Helpers\Helper;use App\Services\LocaleService; @endphp

<div x-data="{
        sizes:[],
        colors:[],
        model:{
            category:[],
            subcategory:[],
            offers: []
        }
    }">

    <style>
        .image-blue img {
            transition: all .3s ease-in-out;
        }

        .image-blue svg {
            z-index: 9;
        }

        .image-blue:hover svg {
            color: white;
        }

        .image-blue img {
            filter: brightness(0.7);
        }

        .image-blue:hover img {
            filter: brightness(0.5);
        }

        .invalid-feedback {
            display: block !important;
        }
    </style>
    <form x-on:submit.prevent="$wire.submit(Object.fromEntries(new FormData($event.target)),model,sizes)"
          class="needs-validation"
          method="post" enctype="multipart/form-data">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="row flex-between-center">
                    <div class="col-md">
                        <h5 class="mb-2 mb-md-0">{{__("product.add")}}</h5>
                    </div>
                    <div class="col-auto">
                        <a href="{{route("product.index")}}" type="button"
                           class="btn btn-link text-secondary p-0 me-3 fw-medium"
                           role="button">{{__("discard")}}</a>
                        <button class="btn btn-primary" type="submit">{{__("save")}} </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-0">
            <div class="col-lg-8 pe-lg-2">
                <div class="card mb-3">
                    <div class="card-header bg-body-tertiary">
                        <h6 class="mb-0">{{__("product.base-info")}}</h6>
                    </div>
                    <div class="card-body">
                        @foreach(LocaleService::getLocaleFields("name") as $el)
                            <div class="row gx-2">
                                <div class="col-12 mb-3">
                                    <label class="form-label"
                                           for="product-name">{{__("$el")}}</label>
                                    <input value="{{old($el)}}"
                                           required
                                           class="form-control {{Helper::isError($errors,$el)}}"
                                           id="product-name" name="{{$el}}" type="text"/>
                                    @error($el)
                                    <div id="validationServer03Feedback" class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header bg-body-tertiary">
                        <h6 class="mb-0">{{__("product.info")}}</h6>
                    </div>

                    <div class="card-body z-[999]">
                        <style>
                            .choices {
                                margin-bottom: 0 !important;
                            }
                        </style>
                        <div wire:ignore class="col-12 mb-3">
                            <label class="form-label"
                                   for="organizerMultiple">{{__("product.size")}}</label>
                            <select class="form-select  js-choice" id="organizerMultiple"
                                    multiple="multiple" size="1"
                                    name="sizes[]" x-on:change="res = [];$event.target.selectedOptions.forEach(function(el){
                                            res.push(el.value)
                                        });$data.sizes = res"
                                    data-options='{"removeItemButton":true,"placeholder":true}'>
                                @foreach($sizes as $size)
                                    <option value="{{$size->id}}">{{$size->name}}</option>
                                @endforeach
                            </select>

                            @error("sizes")
                            <p class="!text-[var(--falcon-danger)]">
                                {{$message}}
                            </p>
                            @enderror
                        </div>
                        <div wire:ignore class="col-12 mb-3">
                            <label class="form-label"
                                   for="organizerMultiple">{{__("product.colors")}}</label>
                            <select class="form-select  js-choice" id="organizerMultiple"
                                    multiple="multiple" size="1"
                                    name="colors[]" x-on:change="res = [];$event.target.selectedOptions.forEach(function(el){

                                            res.push({color:el.value,name:el.innerHTML})
                                        });$data.colors = res;"
                                    data-options='{"removeItemButton":true,"placeholder":true}'>
                                @foreach($colors as $color)
                                    <option value="{{$color->id}}">{{$color->name}}</option>
                                @endforeach
                            </select>

                            @error("sizes")
                            <p class="!text-[var(--falcon-danger)]">
                                {{$message}}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header bg-body-tertiary">
                        <h6 class="mb-0">{{__("product.detail")}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row gx-2" wire:ignore>
                            @foreach(LocaleService::getLocaleFields("desc") as $el)
                                <div class="col-12 mb-3">
                                    <div class="form-floating">
                                        <textarea
                                            class="editor {{Helper::isError($errors,$el)}}"
                                            name="{{$el}}"
                                            placeholder="{{__("$el")}}"
                                            style="height: 100px">{{old($el)}}</textarea>

                                        @error($el)
                                        <div id="validationServer03Feedback" class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-body-tertiary">
                        <h6 class="mb-0">Add images</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">

                            <div class="tab-content">
                                <label for="image" class="w-full">
                                    @if($image)
                                        <div class="mt-3">
                                            <img class="rounded-lg w-full object-fit-cover cursor-pointer"
                                                 src="{{$image->temporaryUrl()}}" alt="">
                                        </div>
                                    @else
                                        <div class="tab-pane preview-tab-pane active" role="tabpanel"
                                             aria-labelledby="tab-dom-2ad5f821-cce1-4c15-803c-4f17505047da"
                                             id="dom-2ad5f821-cce1-4c15-803c-4f17505047da">
                                            <div class="dropzone dropzone-single p-0 dz-clickable">
                                                <div class="dz-preview dz-preview-single"></div>
                                                <div class="dz-message" data-dz-message="data-dz-message">
                                                    <div class="dz-message-text">
                                                        <img class="me-2"
                                                             src="{{asset("assets/img/icons/cloud-upload.svg")}}"
                                                             width="25" alt="">{{__("main:image")}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </label>
                                <input wire:model="image" class="hidden" type="file" id="image"
                                       accept="image/jpeg,image/jpg,image/png">
                            </div>

                            @error("image")
                            <div id="validationServer03Feedback" class="invalid-feedback">
                                {{$message}}
                            </div>
                            @enderror

                        </div>
                        <div class="mb-3">
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
                            <input wire:model="images" class="hidden" type="file" id="images" multiple
                                   accept="image/jpeg,image/jpg,image/png">

                            @error("images")
                            <div id="validationServer03Feedback" class="invalid-feedback">
                                {{$message}}
                            </div>
                            @enderror
                            <div class="images gap-2 grid grid-cols-3 mt-4">

                                @foreach($images as $index=>$image)
                                    <div class="cursor-pointer image-blue relative"
                                         x-on:click="image_modal.colors=colors,image_modal.color = null;image_modal.open = true;image_modal.image = {id:{{$index}},url:'{{$image->temporaryUrl()}}'}">
                                        <h4 class="absolute text-white z-[99] ml-2 mt-2"
                                            style="color: {{$product_colors[$index]->color ?? '#fff'}}!important;">{{$product_colors[$index]->name ?? ''}}</h4>
                                        <img class="rounded-lg"
                                             style="width: 200px;height: 100px;object-fit: cover;{{$product_colors[$index]->color ?? false ? 'filter: brightness(0.4)' : ''}}!important;"
                                             src="{{$image->temporaryUrl()}}"
                                             alt="">
                                    </div>
                                @endforeach
                            </div>
                            @error("product_colors")
                            <div id="validationServer03Feedback" class="invalid-feedback">
                                {{$message}}
                            </div>
                            @enderror

                        </div>

                    </div>
                </div>


            </div>
            <div class="col-lg-4 ps-lg-2">
                <div class="sticky-sidebar">
                    <div class="card mb-3">
                        <div class="card-header bg-body-tertiary">
                            <h6 class="mb-0">Type</h6>
                        </div>
                        <div class="card-body">
                            <div class="row gx-2">

                                <livewire:admin.product.select-category/>
                                <div class="col-12">
                                    <label class="form-label"
                                           for="product-subcategory">{{__("product.select-gender")}}
                                    </label><select required class="form-select {{Helper::isError($errors,"gender")}}"
                                                    id="product-subcategory"
                                                    name="gender">

                                        @foreach(GenderEnum::toArray() as $gender)
                                            <option @if(old("gender") == $gender) selected
                                                    @endif value="{{$gender}}">{{__($gender)}}</option>
                                        @endforeach

                                    </select>
                                    @error("gender")
                                    <div id="validationServer03Feedback" class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>

                                <div class="col-12 mt-3">
                                    <label class="form-label"
                                           for="product-subcategory">{{__("product.select-size-image")}}
                                    </label><select required
                                                    class="form-select {{Helper::isError($errors,"size-image")}}"
                                                    id="product-subcategory"
                                                    name="size-image">
                                        @foreach($sizeImages as $size)
                                            <option @if(old("size-image") == $size->id) selected
                                                    @endif value="{{$size->id}}">{{__($size->name)}}</option>
                                        @endforeach
                                        <option disabled selected>{{ __("not") }}</option>

                                    </select>
                                    @error("size-image")
                                    <div id="validationServer03Feedback" class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-body-tertiary">
                            <h6 class="mb-0">{{__("product.price")}}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row gx-2">
                                <div class="col-12 mb-3"><label class="form-label" for="base-price">{{__("price")}}
                                        <span
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Product regular price"><span
                                                class="fas fa-question-circle text-primary fs-10 ms-1"></span></span></label>
                                    <input required
                                           class="form-control imask-price {{Helper::isError($errors,"price")}}"
                                           value="{{old("price")}}" id="base-price" name="price" type="text"/>
                                    @error("price")
                                    <div id="validationServer03Feedback" class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-4"><label class="form-label"
                                                                for="discount-percentage">{{__("discount")}}</label><input
                                        class="form-control imask-price {{Helper::isError($errors,"discount")}}"
                                        name="discount"
                                        value="{{old("discount")}}" id="discount-percentage"
                                        type="text"/>
                                    @error("discount")
                                    <div id="validationServer03Feedback" class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-body-tertiary">
                            <h6 class="mb-0">{{__("product.status")}}</h6>
                        </div>
                        <div class="card-body">
                            @foreach(ProductStatusEnum::toArray() as $index => $el)
                                <div class="form-check">
                                    <input required @if(old("status") == $el or $index == 0) checked
                                           @endif class="form-check-input p-2 {{Helper::isError($errors,"status")}}"
                                           id="in-stock-{{$el}}" type="radio"
                                           name="status" value="{{$el}}"/>
                                    @error("status")
                                    <div id="validationServer03Feedback" class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                    <label
                                        class="form-check-label fs-9 fw-normal text-700"
                                        for="in-stock-{{{$el}}}">{{__($el)}}</label>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md">
                        <h5 class="mb-2 mb-md-0">{{__("almost:done")}}</h5>
                    </div>
                    <div class="col-auto">
                        <a href="{{route("product.index")}}" type="button"
                           class="btn btn-link text-secondary p-0 me-3 fw-medium"
                           role="button">{{__("discard")}}</a>
                        <button class="btn btn-primary" type="submit">{{__("product.add")}}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <x-slot:ext_footer>
        <x-ext.ckeditor/>
    </x-slot:ext_footer>
</div>
