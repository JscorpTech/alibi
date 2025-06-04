@props([
    "order",
    "route"
])
@php use App\Enums\OrderStatusEnum; @endphp


<div class="dropdown font-sans-serif position-static  rounded-3">
    <button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal"
            type="button"
            id="order-dropdown-29" data-bs-toggle="dropdown" data-boundary="viewport"
            aria-haspopup="true" aria-expanded="false"><span
            class="fas fa-ellipsis-h fs-10"></span></button>
    <div class="dropdown-menu dropdown-menu-end border py-0"
         aria-labelledby="order-dropdown-29">
        <div class="py-2">
            @foreach(OrderStatusEnum::toArray() as $status)
                <form action="{{route("order.edit:status",$order->id)}}" method="post">
                    @csrf
                    <input type="hidden" name="status" value="{{$status}}">
                    <button type="submit" class="dropdown-item">{{__($status)}}</button>
                </form>
            @endforeach
            <div class="dropdown-divider"></div>

            <button x-on:click="delete_confirm('{{route("order.destroy",$order->id)}}?route={{route('order.index')}}')"
                    class="dropdown-item text-danger">{{__("delete")}}</button>
        </div>
    </div>
</div>



