@php use App\Models\Color; @endphp
<div>
    <div :class="image_modal.open ? '!flex' : ''" class="hidden transition">
        <div
            class="backdrop-blur-sm transition overflow-y-scroll pt-[180px] z-[9999] flex justify-center items-center w-[100vw]  fixed h-[100vh]">
            <div class="rounded mt-[-200px] p-4 bg-white mb-[50px] max-w-[500px] w-[90%]">
                <div class="modal-header">
                    <h5>{{__("select:color")}}</h5>
                    <i class="far fa-window-close fs-6 cursor-pointer" x-on:click="image_modal.open = false"></i>
                </div>
                <div class="modal-body mt-4">
                    <form x-on:submit.prevent="$dispatch('setColor',{image:image_modal.image.id,color:image_modal.color});image_modal.open=false">
                        <div class="image  flex justify-center">
                            <div class="relative flex flex-col justify-center">
                                <img class="object-fit-cover max-w-[200px]  rounded-md" :src="image_modal.image.url" alt="">
                            </div>
                        </div>
                        <div class="inputs mt-2">
                            <div class="input">
                                <label class="form-label"
                                       for="product-category">{{__("color")}}</label><select
                                    required
                                    x-model="image_modal.color"
                                    class="form-select"
                                    id="product-category"
                                    name="position">
                                    <option value="none">{{__("none")}}</option>

                                    <template x-for="color in image_modal.colors">
                                        <option :selected="image_modal.color === color.color"
                                                :value="color.color" x-text="color.name"></option>
                                    </template>

                                </select>
                            </div>

                            <div class="w-full mt-3 grid md:grid-cols-2 grid-cols-1 gap-x-4">
                                <button type="button"
                                        x-on:click="$dispatch('deleteImage',{id:image_modal.image.id});image_modal.open=false"
                                        class="w-full bg-[var(--falcon-danger)] text-[#fff] mt-3 rounded-sm py-2">{{__("delete")}}</button>

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
