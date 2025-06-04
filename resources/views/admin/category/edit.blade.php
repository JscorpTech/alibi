@php use App\Enums\GenderEnum;use App\Http\Helpers\Helper;use App\Services\LocaleService; @endphp
@php
    function isError($errors,$el) {
        if($errors->has($el))
            return "is-invalid";
        elseif($errors->any())
            return "is-valid";
    }
@endphp

<x-layouts.main>
    <form enctype="multipart/form-data" action="{{route("category.update",$category->id)}}" class="needs-validation"
          method="post">
        @method("put")
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="row flex-between-center">
                    <div class="col-md">
                        <h5 class="mb-2 mb-md-0">{{__("category.add")}}</h5>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-link text-secondary p-0 me-3 fw-medium"
                                role="button">{{__("discard")}}</button>
                        <button class="btn btn-primary" role="button">{{__("save")}} </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-0">
            <div class="col-12 pe-lg-2">
                <div class="card mb-3">
                    <div class="card-header bg-body-tertiary">
                        <h6 class="mb-0">{{__("category.base-info")}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                            @foreach(LocaleService::getLocaleFields("name") as $el)
                                <div class="row gx-2">
                                    <div class="col-12 mb-3">
                                        <label class="form-label"
                                               for="product-name">{{__("$el")}}</label>
                                        <input value="{{old($el,$category->$el)}}"
                                               class="form-control {{isError($errors,$el)}}"
                                               id="product-name" name="{{$el}}" type="text"/>
                                        @error($el)
                                        <div id="validationServer03Feedback" class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror
                                    </div>

                                </div>
                            @endforeach

                            <div class="row gx-2">
                                <div class="col-12 mb-3">
                                    <label class="form-label"
                                           for="product-name">{{__("position")}}</label>
                                    <input min="1" required value="{{old("position",$category->position)}}"
                                           class="form-control {{isError($errors,"position")}}"
                                           id="product-name" name="{{"position"}}" type="number"/>
                                    @error("position")
                                    <div id="validationServer03Feedback" class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row gx-2">
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
                            </div>
                            <div class="row gx-2">
                                <div class="col-12">
                                    <label class="form-label"
                                           for="product-name">{{__("image")}}</label>
                                    <input min="1" value="{{old("image",1)}}"
                                           class="form-control {{isError($errors,"image")}}"
                                           id="product-name" accept="image/*" name="image" type="file"/>
                                    @error("image")
                                    <div id="validationServer03Feedback" class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
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
                                <button class="btn btn-link text-secondary p-0 me-3 fw-medium"
                                        role="button">{{__("discard")}}</button>
                                <button class="btn btn-primary" role="button">{{__("category.add")}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <x-ext.imask/>
</x-layouts.main>
