@php use App\Http\Helpers\OrderHelper; @endphp
@props([
    "order"
])
<x-layouts.main>
    <div class="card mb-3">
        <div class="bg-holder d-none d-lg-block bg-card"
             style="background-image:url({{asset("assets/img/icons/spot-illustrations/corner-4.png")}});opacity: 0.7;"></div>
        <!--/.bg-holder-->
        <div class="card-body position-relative">
            <div class="d-flex flex-between-center">
                <h5>{{__("order.detail")}}: #{{$order->id}}</h5>
                <x-order.status-action :order="$order" :route="route('order.index')"/>
            </div>
            <p class="fs-10">{{$order->created_at->format("F d, Y, H:i")}}</p>

            <div><strong class="me-2">{{__("order.status")}}: </strong>
                <div
                    class="badge rounded-pill badge-subtle-{{OrderHelper::getStatusColor($order->status)}} fs-11">{{__($order->status)}}
                    <span
                        class="fas {{OrderHelper::getStatusIcon($order->status)}} ms-1"
                        data-fa-transform="shrink-2"></span>
                </div>
            </div>

        </div>

    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4 mb-lg-0">
                    <h5 class="mb-3 fs-9">{{__("order.billing")}}</h5>
                    <h6 class="mb-2">{{__("client")}}: <span>{{$order->user->full_name}}</span></h6>
                    <p class="mb-1 fs-10">{{__("payment:type")}}<span>: {{__($order->payment_type)}}</span></p>

                    <p class="mb-2 fs-10"><strong>{{__("phone")}}: </strong><a
                            href="tel:{{$order->user->phone}}"
                            style="text-decoration: none;color: var(--falcon-primary)">+{{$order->user->phone}}</a>
                    </p>
                    <h6 class="mb-2">{{__("price")}}: <span
                            style="color: var(--falcon-info)">{{number_format($order->getTotalPrice(),2)}} {{ __("currency") }}</span>
                    </h6>
                    <h6 class="mb-2">{{__("discount")}}: <span
                            style="color: var(--falcon-warning)">{{number_format($order->orders()->sum("discount"))}} {{__("currency")}}</span>
                    </h6>
                    <h6 class="mb-2">{{__("Kashbackdan to'ladi")}}: <span
                            style="color: var(--falcon-warning)">{{ number_format($order->cashback ?? 0,2) }} {{__("currency")}}</span>
                    </h6>
                    <h6 class="mb-2">{{__("Kashback berildi")}}: <span
                            style="color: var(--falcon-warning)">{{ number_format($order->given_cashback ?? 0,2) }} {{__("currency")}}</span>
                    </h6>
                </div>
                <div class="col-md-6 col-lg-4 mb-4 mb-lg-0">
                    <h5 class="mb-3 fs-9">{{__("address")}}</h5>
                    <a href="{{route("user.show",$order->user->id)}}"><h6 class="mb-2">{{$order->user->full_name}}</h6>
                    </a>
                    <p class="mb-0 fs-10">{{$order->address?->label}}</p>
                    <p class="mb-0 fs-10">{{$order->address?->region?->name}}</p>
                    <p class="mb-0 fs-10">{{$order->address?->district?->name}}</p>

                    <p class="mb-0 fs-10">
                        <a href="https://yandex.ru/maps/?pt={{$order->address?->long}},{{$order->address?->lat}}&z=18&l=map">
                            {{ __("Yandex maps") }}
                        </a>
                    </p>

                </div>
                <div class="col-md-6 col-lg-4 mb-4 mb-lg-0">
                    <h5 class="mb-3 fs-9">{{__("Qo'shimcha malumot")}}</h5>
                    <p class="mb-0 fs-10">{{ __("Yetkazib berish vaqti: ") }}{{$order->delivery_date}}</p>
                </div>

            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div>
                <h5 class="mb-3 fs-9">{{__("product")}}</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($order->orders as $item)
                        <div class="mt-3 flex-1">
                            <h6 class="mb-2">{{__("name")}}:
                                <a href="{{route("product.show",$item?->product?->id)}}">{{$item?->product?->name}}</a>
                            </h6>

                            <h6 class="mb-2">{{__("count")}}: <span
                                    style="color: var(--falcon-warning)">{{$item->count}} {{__("ta")}}</span>
                            </h6>
                            <h6 class="mb-2">{{__("status")}}: <span
                                    style="color: var(--falcon-green)">{{__($item->product?->status)}}</span></h6>
                            <h6 class="mb-2">{{__("product.size")}}: <span
                                    style="color: var(--falcon-green)">{{__($item->size?->name)}}</span></h6>
                            <h6 class="mb-2">{{__("color")}}: <span
                                    style="color: var(--falcon-green)">{{__($item->color?->name)}}</span></h6>
                            <h6 class="mb-2">{{__("color")}}: <span
                                    style="color: var(--falcon-green)">{{__(number_format($item->getTotalPrice(),2))}} {{ __("currency") }}</span></h6>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.main>
