<div xmlns:wire="http://www.w3.org/1999/xhtml">

    <div class="grid grid-cols-5 gap-3">
        <div class="md:col-span-3 col-span-5 table-responsive scrollbar">
            <div class="card overflow-x-scroll">
                <div class="card-body">
                    <table class="table table-bordered table-striped fs-10 mb-0">
                        <thead class="bg-200">
                        <tr>
                            <th class="text-900 text-center">{{__("key")}}</th>
                            <th class="text-900 text-center">{{__("value")}}</th>
                            <th class="text-900 text-center">{{__("action")}}</th>
                        </tr>
                        </thead>
                        <tbody class="list">
                        @foreach($data as $item)
                            <tr>
                                <td>
                                    <span class="line-clamp-1">{{$item->key}}</span>
                                </td>
                                <td>
                                    <span class="line-clamp-1">{{substr($item->value,0,20)}}</span>
                                </td>
                                <td class="flex justify-end">
                                    <span class="" wire:click='edit({{$item->id}})'><x-far-button  icon="fa-edit" color="warning"/></span>
                                    <span class="" wire:click='delete({{$item->id}})'><x-far-button  icon="fa-trash-alt" color="danger"/></span>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="md:col-span-2 col-span-5">
            <div class="card">
                <div class="card-body">
                    <form method="post" wire:submit="submit">
                        <label for="">
                            {{__("key")}}
                            @if($type == "update")
                                <input disabled required wire:model="key" name="key" type="text" class="form-control mt-1">
                            @else
                                <input required wire:model="key" name="key" type="text" class="form-control mt-1">
                            @endif

                            @error("key")
                            <p class="mt-2 text-[red]">{{$message}}</p>
                            @enderror
                        </label>
                        <label for="">
                            {{__("value")}}
                            <textarea required wire:model="value" class="form-control mt-1" name="value"></textarea>
                            @error("value")
                            <p class="mt-2 text-[red]">{{$message}}</p>
                            @enderror
                        </label>

                        <div class="w-full flex justify-end mt-2">
                            <button class="btn btn-primary">{{__("save")}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-ext.imask/>
</div>
