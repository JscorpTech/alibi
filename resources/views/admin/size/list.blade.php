@php @endphp
<x-layouts.main>

    <livewire:user.size-create/>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row flex-between-center">
                <div class="col-md">
                    <h5 class="mb-2 mb-md-0">{{__("sizes")}}</h5>
                </div>
                <div class="col-auto">
                    <livewire:open-modal params="{is_open:true}" emit="size-create-modal" class="btn btn-primary"
                                         :text="__('create')"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div id="tableExample" data-list='{"valueNames":["name","email","age"],"page":10,"pagination":true}'>
                <div class="table-responsive scrollbar">
                    <table class="table table-bordered table-striped fs-10 mb-0">
                        <thead class="bg-200">
                        <tr>
                            <th class="text-900 sort" data-sort="name">{{__("name")}}</th>
                            <th class="text-900 sort" data-sort="age">{{__("action")}}</th>
                        </tr>
                        </thead>
                        <tbody class="list">
                        @foreach($categories as $category)
                            <tr>
                                <td class="name">{{$category->name}}</td>
                                <td style="width: 150px;white-space: nowrap">
                                    <livewire:open-modal
                                            params="{is_open:true,name:'{{$category->name}}',type:'update',id:'{{$category->id}}'}"
                                            emit="size-create-modal" text="<i class='fas fa-edit text-warning'></i>"
                                            tag="button" class="btn btn-falcon-default btn-sm ml-2"/>
                                    <div x-on:click="delete_confirm('{{route("size.destroy",$category->id)}}')"
                                         class="d-inline">
                                        <x-far-button icon="fa-trash-alt" color="danger"/>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row align-items-center mt-3">
                    <div class="pagination d-none"></div>
                    <div class="col">
                        <p class="mb-0 fs-10">
                            <span class="d-none d-sm-inline-block" data-list-info="data-list-info"></span>
                            <span class="d-none d-sm-inline-block"> &mdash;</span>
                            <a class="fw-semi-bold" href="#!" data-list-view="*">View all<span
                                    class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a
                                class="fw-semi-bold d-none" href="#!" data-list-view="less">View Less<span
                                    class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                        </p>
                    </div>
                    <div class="col-auto d-flex">
                        <button class="btn btn-sm btn-primary" type="button" data-list-pagination="prev">
                            <span>Previous</span></button>
                        <button class="btn btn-sm btn-primary px-4 ms-2" type="button" data-list-pagination="next">
                            <span>Next</span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.main>
