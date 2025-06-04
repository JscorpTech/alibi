@php use App\Enums\BannerEnum;use App\Enums\BannerStatusEnum;use Illuminate\Support\Facades\Storage; @endphp
<x-layouts.main>

    <style>
        .banner-admin-blur:hover .card-img-top {
            filter: opacity(0.5);
        }

        .banner-admin-blur .card-img-top {
            filter: opacity(0.7);
        }

        .transition {
            transition: all .1s ease-in-out;
        }
    </style>


    <x-slot:top>
        <livewire:admin.banner/>
    </x-slot:top>


    <div class="grid md:grid-cols-2 gap-x-5 justify-center gap-y-4">
        @foreach($banners as $banner)
            <div x-on:click="banner.open=true;banner.img=`{{Storage::url($banner->image)}}`;banner.title=`{{$banner->title}}`;banner.subtitle=`{{$banner->subtitle}}`;banner.link=`{{$banner->link}}`;banner.position=`{{$banner->position}}`;banner.status=`{{$banner->status}}`;banner.id={{$banner->id}};banner.link_text=`{{$banner->link_text}}`">
                <div class="cursor-pointer banner-admin-blur card bg-dark text-white overflow-hidden"
                     data-bs-theme="light" style="max-width: 30rem;">
                    <div class="card-img-top transition">
                       @if(explode("/",Storage::mimeType($banner->image))[0] == "video")
                            <video autoplay muted loop class="img-fluid !w-[500px] !h-[400px] object-fit-cover" >
                                <source src="{{Storage::url($banner->image)}}">
                            </video>
                           @else
                            <img class="img-fluid !w-[500px] !h-[400px] object-fit-cover"
                                 src="{{Storage::url($banner->image)}}" alt="Card image"/>
                        @endif

                    </div>
                    <div class="card-img-overlay d-flex align-items-end">
                        <div>
                            <h5 class="card-title text-white">{{$banner->title}}</h5>
                            <p class="card-text text-white">{{$banner->subtitle}}</p>
                            <a class="card-text text-white" href="{{$banner->link}}">Link</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.main>
