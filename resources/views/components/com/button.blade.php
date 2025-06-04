@props([
    "href",
    "title",
    "color",
    "class",
    "text",
    "bg",
    "type",
    "button"
])

<{{($type ?? "") != "submit" ? "a" : "button"}} type="{{$type ?? ""}}" class="btn bg-{{$bg ?? null}} {{$size ?? "btn-sm"}} {{$button ?? ""}} {{$class ?? ""}} me-2 text-{{$color ?? null}}" href="{{$href ?? '#!'}}" data-bs-toggle="tooltip"
   data-bs-placement="top" title="{{$title ?? ''}}">
    {{$text ?? ""}}
</{{($type ?? "") != "submit" ? "a" : "button"}}>
