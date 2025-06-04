@php use App\Enums\GenderEnum;use App\Enums\ProductStatusEnum;use App\Http\Helpers\Helper;use App\Services\LocaleService;use Illuminate\Support\Facades\Storage; @endphp

<div x-data="{
        colors:{{json_encode($old_colors)}},
        sizes:{{json_encode(array_column($product->sizes->toArray(),"id"))}},
        model:{
            category:{{json_encode($old_c)}},
            subcategory:{{json_encode($old_sc)}},
            offers:{{$product->offers ? json_encode($product?->offers) : '[]'}}
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


    <form
        x-on:submit.prevent="$wire.submit(Object.fromEntries(new FormData($event.target)),{{$product->id}},model,sizes)"
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
                        <button class="btn btn-primary" role="button">{{__("save")}} </button>
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
                                    <input value="{{$product->name}}"
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
                                    <option
                                        @if(in_array($size->id,array_column($product->sizes->toArray(),"id"))) selected
                                        @endif value="{{$size->id}}">{{$size->name}}</option>
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
                                    <option
                                        @if(in_array($color->id,array_column($product->colors->pluck("color")->toArray(),"id"))) selected
                                        @endif value="{{$color->id}}">{{$color->name}}</option>
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
                                    <div class="form-floating"><textarea
                                            class="editor {{Helper::isError($errors,$el)}}"
                                            name="{{$el}}"
                                            placeholder="{{__("$el")}}"
                                            style="height: 100px">{!! $product->desc !!}</textarea>

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
                                        <div class="mt-3">
                                            <img class="rounded-lg w-full object-fit-cover cursor-pointer"
                                                 src="{{Storage::url($product->image)}}" alt="">
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

                                <livewire:admin.product.select-category :old_categories="$old_c"
                                                                        :old_subcategories="$old_sc"
                                                                        :offers="$product?->offers"/>
                                <div class="col-12">
                                    <label class="form-label"
                                           for="product-subcategory">{{__("product.select-gender")}}
                                    </label><select required class="form-select {{Helper::isError($errors,"gender")}}"
                                                    id="product-subcategory"
                                                    name="gender">

                                        @foreach(GenderEnum::toArray() as $gender)
                                            <option @if($product->gender == $gender) selected
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
                                            <option
                                                @if(old("size-image",$product->sizeImage?->id) == $size->id) selected
                                                @endif value="{{$size->id}}">{{__($size->name)}}</option>
                                        @endforeach

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
                                           value="{{$product->price}}" id="base-price" name="price" type="text"/>
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
                                        value="{{$product->discount == 0 ? null : $product->discount}}"
                                        id="discount-percentage"
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
                                    <input required @if($product->status == $el or $index == 0) checked
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
                        <button class="btn btn-primary" role="button">{{__("product.add")}}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <x-slot:ext_footer>
        <x-ext.ckeditor/>
    </x-slot:ext_footer>
</div>
