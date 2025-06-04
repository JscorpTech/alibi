@php use App\Enums\RoleEnum;use Illuminate\Support\Facades\Request; @endphp
@props([
    "users"
])

<x-layouts.main>
    <style>
        .line-clamp-1 {
            -webkit-line-clamp: 1; /* Число отображаемых строк */
            display: -webkit-box; /* Включаем флексбоксы */
            -webkit-box-orient: vertical; /* Вертикальная ориентация */
            overflow: hidden;
        }
    </style>

    <div class="row">
        <div class="card col-xl-8 mb-3">
            <div class="card-header">
                <div class="row flex-between-center">
                    <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                        <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">{{__("users")}}</h5>
                    </div>
                    <div class="col-8 col-sm-auto ms-auto text-end ps-0">
                        <div id="orders-actions">
                            <button class="btn btn-sm btn-falcon-default d-xl-none" type="button"
                                    data-bs-toggle="offcanvas" data-bs-target="#ticketOffcanvas"
                                    aria-controls="ticketOffcanvas">
                                <svg class="svg-inline--fa fa-filter fa-w-16" data-fa-transform="shrink-4 down-1"
                                     aria-hidden="true" focusable="false" data-prefix="fas" data-icon="filter"
                                     role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                     data-fa-i2svg="" style="transform-origin: 0.5em 0.5625em;">
                                    <g transform="translate(256 256)">
                                        <g transform="translate(0, 32)  scale(0.75, 0.75)  rotate(0 0 0)">
                                            <path fill="currentColor"
                                                  d="M487.976 0H24.028C2.71 0-8.047 25.866 7.058 40.971L192 225.941V432c0 7.831 3.821 15.17 10.237 19.662l80 55.98C298.02 518.69 320 507.493 320 487.98V225.941l184.947-184.97C520.021 25.896 509.338 0 487.976 0z"
                                                  transform="translate(-256 -256)"></path>
                                        </g>
                                    </g>
                                </svg>
                                <!-- <span class="fas fa-filter" data-fa-transform="shrink-4 down-1"></span> Font Awesome fontawesome.com --><span
                                    class="ms-1 d-none d-sm-inline-block">Filter</span></button>
                            <button class="btn btn-falcon-default btn-sm" type="button"><span
                                    class="fas fa-external-link-alt"
                                    data-fa-transform="shrink-3 down-2"></span><span
                                    class="d-none d-sm-inline-block ms-1">Export</span></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="tableExample" data-list='{"valueNames":["name","email","age"],"page":10,"pagination":true}'>
                    <div class="table-responsive scrollbar">
                        <table class="table table-bordered table-striped fs-10 mb-0">
                            <thead class="bg-200">
                            <tr>
                                <th class="text-900 sort" data-sort="name">{{__("id")}}</th>
                                <th class="text-900 sort" data-sort="name">{{__("full_name")}}</th>
                                <th class="text-900 sort" data-sort="name">{{__("phone")}}</th>
                                <th class="text-900 sort" data-sort="name">{{__("role")}}</th>
                                <th class="text-900 sort" data-sort="name">{{__("action")}}</th>
                            </tr>
                            </thead>
                            <tbody class="list">
                            @foreach($users as $user)
                                <tr>
                                    <td class="name"><a href="{{route("user.show",$user->id)}}">#{{$user->id}}</a></td>
                                    <td class="name" style="min-width: 175px"><p
                                            class="line-clamp-1">{{$user->full_name}}</p></td>
                                    <td class="name">+{{$user->phone}}</td>
                                    <td class="name">{{$user->role}}</td>
                                    <td style="width: 150px;white-space: nowrap">
                                        <div class="d-inline"
                                             x-on:click="delete_confirm('{{route("user.destroy",$user->id)}}')">
                                            <x-far-button icon="fa-trash-alt" color="danger" type="submit"/>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row align-items-center mt-3">
                        <x-pagination :object="$users"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div
                class="offcanvas offcanvas-end offcanvas-filter-sidebar border-0 dark__bg-card-dark h-auto rounded-xl-3"
                tabindex="-1" id="ticketOffcanvas" aria-labelledby="ticketOffcanvasLabel">
                <div class="offcanvas-header d-flex flex-between-center d-xl-none bg-body-tertiary">
                    <h6 class="fs-9 mb-0 fw-semi-bold">{{__("filter")}}</h6>
                    <button class="btn-close text-reset d-xl-none shadow-none" id="ticketOffcanvasLabel" type="button"
                            data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="card scrollbar shadow-none shadow-show-xl">
                    <div class="card-header bg-body-tertiary d-none d-xl-block">
                        <h6 class="mb-0">{{__("filter")}}</h6>
                    </div>
                    <form>
                        <div class="card-body">
                            <div class="mb-3 mt-n2">
                                <x-com.select :list="RoleEnum::toArray()" :label="__('role')" name="role"/>
                            </div>
                            <div class="mb-3 mt-n2">
                                <x-com.input :label="__('full_name')" :value="Request::get('full_name')"
                                             name="full_name"/>
                            </div>
                            <div class="mb-3 mt-n2">
                                <x-com.input name="phone" :label="__('phone')"
                                             :value="Request::get('phone','998') != '' ? Request::get('phone','998') : '998'"
                                             class="imask-phone"/>
                            </div>
                        </div>
                        <div class="card-footer border-top border-200 py-x1">
                            <button type="submit" class="btn btn-primary w-100">{{__("update")}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-ext.imask/>
</x-layouts.main>
