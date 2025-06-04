@php use App\Enums\GenderEnum; @endphp
<div>
    @if($select_gender)
        <div
                class="fixed w-[100vw] h-[100vh] backdrop-blur-sm backdrop-brightness-[0.7] flex justify-center items-center z-[99999]">
            <div class="rounded-md p-3 min-h-[100px] w-[90%] max-w-[500px] bg-white">
                <div class="modal-header p-4">
                    <p>{{ __("select:gender") }}</p>
                </div>
                <div class="modal-body grid grid-cols-2 gap-3">
                    <div wire:click="$dispatch('selectGender',{gender:'{{GenderEnum::MALE}}'})"
                         class="card cursor-pointer transition hover:brightness-50">
                        <img class="rounded-sm" src="{{ asset("assets/img/male.png") }}" alt="">
                    </div>
                    <div wire:click="$dispatch('selectGender',{gender:'{{GenderEnum::FEMALE}}'})"
                         class="card cursor-pointer transition hover:brightness-50">
                        <img class="rounded-sm" src="{{ asset("assets/img/female.png") }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
