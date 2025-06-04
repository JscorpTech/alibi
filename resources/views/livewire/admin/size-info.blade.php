@php use Illuminate\Support\Facades\Storage; @endphp
<div>
    <x-slot:top>
        <x-admin.modal>
            <livewire:admin.size-info-create/>
        </x-admin.modal>
    </x-slot:top>

    <div class="card mb-3">
        <div class="card-header flex justify-end">
            <button x-on:click="modal.toggle()" class="btn btn-primary">{{__("create")}}</button>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 grid-cols-1 gap-3">
        @foreach($data as $datum)
            <div class="flex justify-start items-end relative">
                <div class="flex justify-between w-full absolute z-[9999]">
                    <h4 class="line-clamp-1 text-white  ml-3 mb-2">{{$datum->name}}</h4>
                    <span wire:click="delete({{$datum->id}})">
                        <x-far-button icon="fa fa-trash-alt" color="danger"/>
                    </span>
                </div>
                <div class="grid gap-x-1 grid-cols-2 rounded-2 overflow-hidden brightness-[0.6] cursor-pointer">
                    <div class="left">
                        <img src="{{Storage::url($datum->image_1)}}" class="h-[200px] w-full object-fit-cover" alt="">
                    </div>
                    <div class="right">
                        <img src="{{Storage::url($datum->image_2)}}" class="h-[200px] w-full object-fit-cover" alt="">
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        window.addEventListener("alpine:init", () => {
            Alpine.data("vars", () => ({
                init() {
                    window.addEventListener("closeModal", () => {
                        this.modal.open = false;
                    })
                },
                modal: {
                    toggle() {
                        this.open = !this.open;
                    },
                    open: false
                }
            }));
        });
    </script>
</div>
