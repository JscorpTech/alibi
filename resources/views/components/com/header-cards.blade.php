@props([
    "cards",
    "font"
])

<div class="row g-3 mb-3">

    @foreach($cards as $card)
        <div class="col-sm-6 col-md-4">
            <div class="card overflow-hidden" style="min-width: 12rem">
                <div class="bg-holder bg-card"
                     style="background-image:url({{$card["image"] ?? asset("assets/img/icons/spot-illustrations/corner-1.png")}});"></div>
                <!--/.bg-holder-->
                <div class="card-body position-relative">
                    <h6>{{$card['name']}}</h6>
                    <div class="white-space-nowrap display-4 {{$font ?? "fs-5"}} mb-2 fw-normal font-sans-serif text-warning"
                         data-countup="{&quot;endValue&quot;:58.386,&quot;decimalPlaces&quot;:2,&quot;suffix&quot;:&quot;k&quot;}">{{$card['value']}}</div>
                </div>
            </div>
        </div>
    @endforeach

</div>
