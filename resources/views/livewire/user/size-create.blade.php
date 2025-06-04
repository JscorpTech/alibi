@php use App\Http\Helpers\Helper; @endphp
<div>

    @if($is_open ?? false)
        <div class="modal fade  d-block opacity-100 backdrop-filter backdrop-blur-sm" style="transition: 2s">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
                <div class="modal-content position-relative">
                    <div class="position-absolute top-0 end-0 mt-2 me-2 z-1">
                        <button wire:click="$set('is_open',false)"
                                class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">

                        <div class="p-4 pb-5 pt-5" x-data="{
                            name:$wire.name
                        }">
                            <form x-on:submit.prevent="$wire.submit(name)" action="{{route("size.store")}}" class="needs-validation" method="post"
                                  enctype="multipart/form-data">
                                @csrf

                                <div class="row g-0">
                                    <div class="col-12 pe-lg-2">
                                        <div class="card mb-3">
                                            <div class="card-header bg-body-tertiary">
                                                <h6 class="mb-0">{{__("size.base-info")}}</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row gx-2">
                                                    <div class="col-12 mb-3">
                                                        <label class="form-label"
                                                               for="product-name">{{__("name")}}</label>
                                                        <input required min="1" x-model="name" value="{{old("name")}}"
                                                               class="form-control @if($is_error ?? false) is-invalid @endif"
                                                               id="product-name" name="name" type="text"/>
                                                        @if($is_error ?? false)
                                                            <div id="validationServer03Feedback"
                                                                 class="invalid-feedback">
                                                                {{$message ?? ""}}
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
                                            <div class="col-auto">
                                                <button wire:click="$dispatch('size-create-modal',{is_open:false})"
                                                        class="btn btn-link text-secondary p-0 me-3 fw-medium"
                                                        type="button"
                                                        role="button">{{__("discard")}}</button>
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit"
                                                        class="btn btn-primary" role="button">{{__("save")}}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <x-ext.imask/>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>


