@php use App\Enums\GenderEnum;use App\Enums\ProductStatusEnum;use App\Services\LocaleService; @endphp
@php
    function isError($errors,$el) {
        if($errors->has($el))
            return "is-invalid";
        elseif($errors->any())
            return "is-valid";
            return "is-valid";
    }
@endphp

<x-layouts.main>
    <form action="{{route("size.update",$size->id)}}" class="needs-validation" method="post">
        @method("put")
        @csrf
        <div class="row g-0">
            <div class="col-12 pe-lg-2">
                <div class="card mb-3">
                    <div class="card-header bg-body-tertiary">
                        <h6 class="mb-0">{{__("base-info")}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row gx-2">
                            <div class="col-12 mb-3">
                                <label class="form-label"
                                       for="product-name">{{__("name")}}</label>
                                <input value="{{old("name",$size->name)}}"
                                       class="form-control {{isError($errors,"name")}}"
                                       id="product-name" name="name" type="text"/>
                                @error("name")
                                <div id="validationServer03Feedback" class="invalid-feedback">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>

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
                        <button class="btn btn-primary" role="button">{{__("add")}}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <x-ext.imask/>
</x-layouts.main>
