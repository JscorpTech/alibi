@props([
    "href",
    "icon",
    "title",
    "color",
    "class",
    "text",
    "bg",
    "type",
    "attr",
])

<{{($type ?? "") != "submit" ? "a" : "button"}} type="{{$type ?? ""}}" {{$attr ?? ""}} class="btn bg-{{$bg ?? null}} btn-sm btn-falcon-default {{$class ?? ""}} me-2 text-{{$color ?? null}}" href="{{$href ?? '#!'}}" data-bs-toggle="tooltip"
   data-bs-placement="top" title="{{$title ?? ''}}">
    <i {{$class ?? ""}} class="far {{$icon}} text-{{$color ?? null}}"></i>
    {{$text ?? ""}}
</{{($type ?? "") != "submit" ? "a" : "button"}}>
