<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        <a href="https://yandex.ru/maps/?pt={{$long}},{{$lat}}&z=18&l=map">Ko'rish</a>
    </div>
</x-dynamic-component>
