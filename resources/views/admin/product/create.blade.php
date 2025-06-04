@php use App\Enums\GenderEnum;use App\Enums\ProductStatusEnum;use App\Services\LocaleService; @endphp
@php
    function isError($errors,$el) {
        if($errors->has($el))
            return "is-invalid";
        elseif($errors->any())
            return "is-valid";
    }
@endphp

<x-layouts.main>

    <x-slot:top>
        <livewire:admin.image-color-modal/>
    </x-slot:top>

    <livewire:admin.product-create/>

    <x-ext.imask/>
    <slot:ext_header>
        <link href="{{asset("vendors/choices/choices.min.css")}}" rel="stylesheet"/>
    </slot:ext_header>
    <slot:ext_footer>
        <script src="{{asset("vendors/choices/choices.min.js")}}"></script>
    </slot:ext_footer>

</x-layouts.main>
