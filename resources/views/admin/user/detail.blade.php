@php use App\Http\Helpers\OrderHelper;use Illuminate\Support\Facades\Storage; @endphp
<x-layouts.main>
    <x-slot:ext_header>
        <style>
            .user-orders-card {
                display: flex;
                flex-direction: row;
            }

            @media (max-width: 770px) {
                .user-orders-card {
                    display: block;
                }

                .user-orders-card img {
                    width: 100% !important;
                }
            }
        </style>
    </x-slot:ext_header>
    <div class="card mb-3">
        <div class="card-header position-relative min-vh-25 mb-7">
            <div class="bg-holder rounded-3 rounded-bottom-0"
                 style="background-image:url({{asset("assets/img/generic/4.jpg")}});"></div><!--/.bg-holder-->
            <div class="avatar avatar-5xl avatar-profile"><img class="rounded-circle img-thumbnail shadow-sm"
                                                               src="{{asset("assets/img/team/2.jpg")}}" width="200"
                                                               alt=""/>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <h4 class="mb-3"> {{$user->full_name}}</h4>
                    <p class="">{{__("phone")}}: +{{$user->phone}}</p>
                    <p class="">{{__("created_at")}}: {{$user->created_at->format("F d, Y")}}</p>
                    <p class="">{{__("orders")}}: {{$user->OrderGroup()->count()}} {{__("ta")}}</p>
                    <p class="">{{__("balance")}}: {{number_format($user->balance,2)}} {{__("currency")}}</p>
                </div>

            </div>
        </div>
    </div>
    <div class="card mb-3">

        <div class="card-body">
            <div class="row row-gap-4">
                <ul role="list" class="divide-y divide-gray-100">
                    @foreach($user->OrderGroup()->orderByDesc("id")->get() as $orders)
                        @foreach($orders->orders as $order)
                            @php
                                if($order->product == null) continue
                            @endphp
                            <li class="flex justify-between gap-x-6 pt-3">
                                <div class="flex min-w-0 gap-x-4">
                                    <img class="h-12 w-12 flex-none rounded-full bg-gray-50"
                                         src="{{Storage::url($order->product->image)}}"
                                         alt="">
                                    <div class="min-w-0 flex-auto">
                                        <livewire:open-modal params="{data:{{$order->product->id}}}"
                                                             text="{{$order->product->name}}"/>


                                        <p class="text-sm font-semibold leading-6 text-gray-900"></p>
                                        <p class="mt-1 truncate text-xs leading-5 text-gray-500 lime-clamp-2">{!!  $order->product->desc !!}</p>
                                    </div>
                                </div>
                                <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                                    <p class="text-sm leading-6 text-gray-900 text-warning">{{number_format($order->price)}} {{__("currency")}}</p>
                                    <div
                                        class="badge rounded-pill badge-subtle-{{OrderHelper::getStatusColor($orders->status)}} fs-11">
                                        <a class="text-{{OrderHelper::getStatusColor($orders->status)}}"
                                           style="text-decoration: none" href="{{route("order.show",$orders->id)}}">
                                            {{__($order->status)}}
                                            <span
                                                class="fas {{OrderHelper::getStatusIcon($order->status)}} ms-1"
                                                data-fa-transform="shrink-2"></span>
                                        </a>
                                    </div>
                                    <p class="mt-1 text-xs leading-5 text-gray-500">
                                        <time
                                            datetime="2023-01-23T13:23Z">{{$order->created_at->format("F, d, Y H:i")}}</time>
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-layouts.main>
