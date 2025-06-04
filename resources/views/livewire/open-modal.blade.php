<{{$tag}} wire:click="$dispatch('{{$emit ?? "editContent"}}',{{$params}})" class="{{$class ?? ""}}" type="button">
{!! $text ?? "Open Modal" !!}
</{{$tag}}>

