@props([
    "label",
    "name",
    "class",
    "list"
])

<label class="mb-1">{{$label ?? ""}}</label>
<select name="{{$name ?? ""}}" class="form-select {{$class ?? ""}} form-select-sm">
    <option value="all">{{__("all")}}</option>
    @foreach($list ?? [] as $item)
        <option @if(Request::get($name ?? "") == $item) selected
                @endif value="{{$item}}">{{__($item)}}</option>
    @endforeach
</select>
