@php use App\Http\Helpers\Helper; @endphp
<div>
    <form class="needs-validation" wire:submit="submit">
        <div class="row gx-2">
            <div class="col-12 mb-3">
                <label class="form-label"
                       for="product-name">{{__("name")}}</label>
                <input wire:model="name" value="{{old("name")}}"
                       required
                       class="form-control {{Helper::isError($errors,"name")}}"
                       id="product-name" name="{{"name"}}" type="text"/>
                @error("name")
                <div id="validationServer03Feedback" class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>

        </div>
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
                <input name="image" wire:model="image" class="hidden" type="file" id="image"
                       accept="image/jpeg,image/jpg,image/png">
            </div>

            @error("image")
            <div id="validationServer03Feedback" class="invalid-feedback" style="display: inline-block">
                {{$message}}
            </div>
            @enderror

        </div>
        <div class="mb-3">
            <div class="tab-content">
                <label for="image2" class="w-full">
                    @if($image2)
                        <div class="mt-3">
                            <img class="rounded-lg w-full object-fit-cover cursor-pointer"
                                 src="{{$image2->temporaryUrl()}}" alt="">
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
                <input name="image2" wire:model="image2" class="hidden" type="file" id="image2"
                       accept="image/jpeg,image/jpg,image/png">
            </div>

            @error("image2")
            <div id="validationServer03Feedback" class="invalid-feedback" style="display: inline-block">
                {{$message}}
            </div>
            @enderror

        </div>
        <div class="bottom flex justify-end">
            <button type="submit" class="btn btn-primary">{{__("save")}}</button>
        </div>
    </form>

</div>
