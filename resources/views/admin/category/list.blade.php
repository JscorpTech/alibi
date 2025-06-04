@php @endphp
<x-layouts.main>
    <div class="card mb-3">
        <div class="card-body">
            <div id="tableExample" data-list='{"valueNames":["name","email","age"],"page":10,"pagination":true}'>
                <div class="table-responsive scrollbar">
                    <table class="table table-bordered table-striped fs-10 mb-0">
                        <thead class="bg-200">
                        <tr>
                            <th class="text-900 sort" data-sort="name">{{__("name")}}</th>
                            <th class="text-900 sort" data-sort="age">{{__("products")}}</th>
                            <th class="text-900 sort" data-sort="age">{{__("position")}}</th>
                            <th class="text-900 sort" data-sort="age">{{__("gender")}}</th>
                            <th class="text-900 sort" data-sort="age">{{__("action")}}</th>
                        </tr>
                        </thead>
                        <tbody class="list">
                        @foreach($categories as $category)
                            <tr>
                                <td class="name">{{$category->name}}</td>
                                <td class="age">{{$category->products->count()}}</td>
                                <td class="age">{{$category->position}}</td>
                                <td class="age">{{ __($category->gender) }}</td>
                                <td style="width: 150px;white-space: nowrap">
                                    <x-far-button icon="fa-edit" color="warning"
                                                  :href="route('category.edit',$category->id)"/>
                                    <div class="d-inline"
                                         x-on:click="delete_confirm('{{route("category.destroy",$category->id)}}')">
                                        <x-far-button icon="fa-trash-alt" color="danger" type="submit"/>
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
