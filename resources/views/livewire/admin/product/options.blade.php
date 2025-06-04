<div xmlns:wire="http://www.w3.org/1999/xhtml" x-data="options">

    <div class="card">
        <div class="card-header flex">
            <h5>{{__("product:options")}}</h5>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">
            <form action="" wire:submit="submit(Object.fromEntries(new FormData($event.target)))">
                <div class="grid grid-cols-2 gap-3">
                    @foreach($product->colors as $color)
                        <div>
                            <div class="table-responsive scrollbar">
                                <table class="table table-bordered table-striped fs-10 mb-0">
                                    <thead class="bg-200">
                                    <tr>
                                        <th class="text-900 text-center">{{__("size")}}</th>
                                        <th class="text-900 text-center">{{__("count")}}</th>
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    <tr>
                                        <td colspan="2"><p
                                                    class="text-center text-[20px] font-bold">{{$color->color->name}}</p>
                                        </td>
                                    </tr>
                                    @foreach($product->sizes as $size)
                                        <tr>
                                            <td class="name text-center">{{$size->name}}</td>
                                            <td class="email">
                                                <label for="">
                                                    <input value="{{\App\Http\Helpers\LivewireHelper::getValue($product->id,$size->id,$color->color->id)}}" required=""
                                                           class="form-control imask-price"
                                                           name="count[{{$size->id}}-{{$color->color->id}}]"
                                                           type="text">
                                                </label>

                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    @endforeach
                </div>
               <div class="flex justify-between mt-3">
                   <a type="button" href="{{route("product.show",$product->id)}}" class="btn btn-info">{{__("back")}}</a>
                   <button type="submit" class="btn btn-primary">{{__("save")}}</button>
               </div>
            </form>

        </div>
    </div>
    <x-ext.imask/>
    <script>
        window.addEventListener("alpine:init", () => {
            Alpine.data("options", () => ({}))
        })
    </script>
</div>
