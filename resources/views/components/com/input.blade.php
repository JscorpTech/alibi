@props([
    "label",
    "name",
    "class",
    "value"
])
<label style="margin: 0px">{{$label ?? ""}}
    <input name="{{$name ?? "input"}}" type="text"
           value="{{$value ?? ""}}"
           class="form-control {{$class ?? ""}}">
</label>
