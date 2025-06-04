@php use App\Enums\BannerEnum;use App\Enums\BannerStatusEnum; @endphp
<div x-data="{
            loadImage: (e)=>{
                file = e.target.files[0];
                url = URL.createObjectURL(file)
                $data.banner.img = url;
            },
            loading:false,
            progress:0,
            is_error:false,
            submit:(e)=>{
                  $wire.submit(banner.id,banner.position,banner.status,banner.title,banner.subtitle,banner.link,banner.link_text);
            }
        }">
    <div :class="banner.open ? '!visible' : ''" class="invisible transition">
        <div :class="banner.open ? '!opacity-1 backdrop-blur-sm' : 'opacity-0'"
             class="transition overflow-y-scroll pt-[400px] z-[9999] flex justify-center items-center w-[100vw]  fixed h-[100vh]">
            <div class="rounded p-4 bg-white mb-[50px] max-w-[500px] w-[90%]">
                <div class="modal-header">
                    <h5>{{__("change:banner")}}</h5>
                    <i class="far fa-window-close fs-6 cursor-pointer" x-on:click="banner.open = false"></i>
                </div>
                <div class="modal-body mt-4">
                    <form x-on:submit.prevent="submit">
                        <div class="image  flex justify-center">
                            <div class="relative flex flex-col justify-center"
                                 x-on:livewire-upload-start="loading=true"
                                 x-on:livewire-upload-finish="loading=false"
                                 x-on:livewire-upload-error="is_error=true"
                                 x-on:livewire-upload-progress="progress = $event.detail.progress"
                            >
                                <img class="object-fit-cover max-w-[200px]  rounded-md" :src="banner.img" alt="">
                                <label for="banner-image" class="flex justify-center"><p
                                        class="inline-block py-[10px] px-[20px] bg-[#3498db] text-[#fff] rounded-sm cursor-pointer mt-3">
                                        Select image</p></label>
                                <input wire:model="image" x-model="banner.image" name="img" x-on:change="loadImage"
                                       id="banner-image"
                                       class="hidden" type="file"
                                       accept="image/*, video/*">

                                <div x-show="loading" class="progress mb-3" style="height:15px" role="progressbar"
                                     aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                    <div x-cloak="" class="progress-bar" :style="`width: ${progress}%`"
                                         x-text="`${progress}%`"></div>
                                </div>

                                <div x-show="is_error">
                                    <h5 class="text-red-500 text-center">{{__("file:upload:error")}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="inputs mt-2">
                            <div class="input mt-2">
                                <label class="form-label"
                                       for="product-category">{{__("position")}}</label><select
                                    required
                                    x-model="banner.position"
                                    class="form-select"
                                    id="product-category"
                                    name="position">
                                    @foreach(BannerEnum::toArray() as $position)
                                        <option :selected="banner.position === '{{$position}}'"
                                                value="{{$position}}">{{__($position)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input mt-2">
                                <label class="form-label"
                                       for="product-category">{{__("status")}}</label><select
                                    required
                                    x-model="banner.status"
                                    class="form-select"
                                    id="product-category"
                                    name="status">
                                    @foreach(BannerStatusEnum::toArray() as $status)
                                        <option :selected="banner.status === '{{$status}}'"
                                                value="{{$status}}">{{__($status)}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input mt-2">
                                <label for="banner-title">{{__("title")}}</label>
                                <input
                                    x-model="banner.title"
                                    required
                                    class="form-control" id="banner-title" name="title" type="text"
                                    placeholder="{{__("title")}}"/>
                            </div>

                            <div class="input mt-2">
                                <label for="banner-title">{{__("link_text")}}</label>
                                <input
                                    x-model="banner.link_text"
                                    required
                                    class="form-control" id="banner-title" name="link_text" type="text"
                                    placeholder="{{__("link_text")}}"/>
                            </div>
                            <div class="input mt-2">
                                <label for="banner-subtitle">{{__("subtitle")}}</label>
                                <input
                                    required
                                    x-model="banner.subtitle"
                                    class="form-control" id="banner-subtitle" name="name" type="text"
                                    placeholder="{{__("subtitle")}}"/>
                            </div>
                            <div class="input mt-2">
                                <label for="banner-link">{{__("link")}}</label>
                                <input
                                    required
                                    x-model="banner.link"
                                    class="form-control" id="banner-link" name="link" type="url"
                                    placeholder="{{__("link")}}"/>
                            </div>

                            <div class="w-full">
                                <button type="submit"
                                        class="w-full bg-[#3498db] text-[#fff] mt-3 rounded-sm py-2">{{__("save")}}</button>
                            </div>


                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
