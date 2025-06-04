@props([
    "info"
])
<x-layouts.main>
    <div class="row g-3 mb-3">

        <div class="col-md-6 col-lg-4 col-xl-6 col-xxl-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">{{__("month:salas")}}</h6>
                </div>
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-auto align-self-end">
                            <div
                                    class="fs-5 fw-normal font-sans-serif text-700 lh-1 mb-1">{{number_format($monthOrdersPrice)}} {{__("currency")}}</div>
                            <span class="badge rounded-pill fs-11 text-primary">
                                </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-6 col-xxl-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">{{__("total:salas")}}</h6>
                </div>
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-auto align-self-end">
                            <div
                                    class="fs-5 fw-normal font-sans-serif text-700 lh-1 mb-1">{{number_format($ordersPrice)}} {{__("currency")}}</div>
                            <span class="badge rounded-pill fs-11 text-primary">
                                </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-6 col-xxl-4">
            <div class="card h-md-100">
                <div class="card-body">
                    <div class="row h-100 justify-content-between g-0">
                        <div class="col-5 col-sm-6 col-xxl pe-2">
                            <h6 class="mt-1">{{__("categories:salas")}}</h6>
                            <div class="fs-11 mt-3">
                                @php
                                    $i=0;
                                @endphp
                                @foreach($categories as $category)
                                    <div class="d-flex flex-between-center mb-1">
                                        <div class="d-flex align-items-center"><span class="dot"
                                                                                     style="background-color: green;opacity: {{$category['value']}}%"></span><span
                                                    class="fw-semi-bold">{{$category['name']}}</span></div>
                                        <div class="d-xxl-none">{{$category['value']}}%</div>
                                    </div>
                                    @if($i >= 3)
                                        @break
                                    @endif
                                @endforeach

                            </div>

                        </div>
                        <div class="col-auto position-relative">
                            <div class="echart-market-share" _echarts_instance_="ec_1703486318924"
                                 style="user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); position: relative;">
                                <div
                                        style="position: relative; width: 106px; height: 106px; padding: 0px; margin: 0px; border-width: 0px;">
                                    <canvas data-zr-dom-id="zr_0" width="132" height="132"
                                            style="position: absolute; left: 0px; top: 0px; width: 106px; height: 106px; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); padding: 0px; margin: 0px; border-width: 0px;"></canvas>
                                </div>
                                <div class=""></div>
                            </div>
                            <div
                                    class="position-absolute top-50 start-50 translate-middle text-1100 fs-7">{{$monthOrders->count()}} {{__("ta")}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-6 col-xxl-4">
            <div class="card h-md-100">
                <div class="card-body">
                    <div class="row h-100 justify-content-between g-0">
                        <div class="col-5 col-sm-6 col-xxl pe-2">
                            <h6 class="mt-1">{{__("info")}}</h6>
                            <div class="fs-11 mt-3">
                                @foreach($info as $item)
                                    <div class="d-flex flex-between-center mb-1">
                                        <div class="d-flex align-items-center"><span class="dot"
                                                                                     style="background-color: {{$item['color'] ?? "green"}}"></span><span
                                                    class="fw-semi-bold">{{$item['name']}}</span></div>
                                        <div class="d-xxl-none">{{$item['value']}}</div>
                                    </div>
                                @endforeach

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row flex-between-center g-0">
                            <div class="col-6 d-lg-block flex-between-center">
                                <h6 cla ss="mb-2 text-900">{{__("total:users")}}</h6>
                                <h4 class="fs-6 fw-normal text-700 mb-0">{{$users}} {{__("ta")}}</h4>
                            </div>
                            <div class="col-auto h-100">
                                <div style="height: 50px; min-width: 80px; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); position: relative;"
                                     data-echarts="{&quot;xAxis&quot;:{&quot;show&quot;:false,&quot;boundaryGap&quot;:false},&quot;series&quot;:[{&quot;data&quot;:[3,7,6,8,5,12,11],&quot;type&quot;:&quot;line&quot;,&quot;symbol&quot;:&quot;none&quot;}],&quot;grid&quot;:{&quot;right&quot;:&quot;0px&quot;,&quot;left&quot;:&quot;0px&quot;,&quot;bottom&quot;:&quot;0px&quot;,&quot;top&quot;:&quot;0px&quot;}}"
                                     _echarts_instance_="ec_1703486318935">
                                    <div style="position: relative; width: 80px; height: 50px; padding: 0px; margin: 0px; border-width: 0px; cursor: pointer;">
                                        <canvas data-zr-dom-id="zr_0" width="100" height="62"
                                                style="position: absolute; left: 0px; top: 0px; width: 80px; height: 50px; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); padding: 0px; margin: 0px; border-width: 0px;"></canvas>
                                    </div>
                                    <div class=""></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row flex-between-center g-0">
                            <div class="col-6 d-lg-block flex-between-center">
                                <h6 class="mb-2 text-900">{{__("products")}}</h6>
                                <h4 class="fs-6 fw-normal text-700 mb-0">{{$products}} {{__("ta")}}</h4>
                            </div>
                            <div class="col-auto h-100">
                                <div style="height: 50px; min-width: 80px; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); position: relative;"
                                     data-echarts="{&quot;xAxis&quot;:{&quot;show&quot;:false,&quot;boundaryGap&quot;:false},&quot;series&quot;:[{&quot;data&quot;:[3,7,6,8,5,12,11],&quot;type&quot;:&quot;line&quot;,&quot;symbol&quot;:&quot;none&quot;}],&quot;grid&quot;:{&quot;right&quot;:&quot;0px&quot;,&quot;left&quot;:&quot;0px&quot;,&quot;bottom&quot;:&quot;0px&quot;,&quot;top&quot;:&quot;0px&quot;}}"
                                     _echarts_instance_="ec_1703486318935">
                                    <div style="position: relative; width: 80px; height: 50px; padding: 0px; margin: 0px; border-width: 0px; cursor: pointer;">
                                        <canvas data-zr-dom-id="zr_0" width="100" height="62"
                                                style="position: absolute; left: 0px; top: 0px; width: 80px; height: 50px; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); padding: 0px; margin: 0px; border-width: 0px;"></canvas>
                                    </div>
                                    <div class=""></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row flex-between-center g-0">
                            <div class="col-6 d-lg-block flex-between-center">
                                <h6 class="mb-2 text-900">{{__("categories")}}</h6>
                                <h4 class="fs-6 fw-normal text-700 mb-0">{{$cCount}} {{__("ta")}}</h4>
                            </div>
                            <div class="col-auto h-100">
                                <div style="height: 50px; min-width: 80px; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); position: relative;"
                                     data-echarts="{&quot;xAxis&quot;:{&quot;show&quot;:false,&quot;boundaryGap&quot;:false},&quot;series&quot;:[{&quot;data&quot;:[3,7,6,8,5,12,11],&quot;type&quot;:&quot;line&quot;,&quot;symbol&quot;:&quot;none&quot;}],&quot;grid&quot;:{&quot;right&quot;:&quot;0px&quot;,&quot;left&quot;:&quot;0px&quot;,&quot;bottom&quot;:&quot;0px&quot;,&quot;top&quot;:&quot;0px&quot;}}"
                                     _echarts_instance_="ec_1703486318935">
                                    <div style="position: relative; width: 80px; height: 50px; padding: 0px; margin: 0px; border-width: 0px; cursor: pointer;">
                                        <canvas data-zr-dom-id="zr_0" width="100" height="62"
                                                style="position: absolute; left: 0px; top: 0px; width: 80px; height: 50px; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); padding: 0px; margin: 0px; border-width: 0px;"></canvas>
                                    </div>
                                    <div class=""></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot:ext_header>
        <script src="{{asset("vendors/echarts/echarts.min.js")}}"></script>
    </x-slot:ext_header>
</x-layouts.main>


