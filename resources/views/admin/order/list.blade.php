@php use App\Enums\OrderStatusEnum;use App\Http\Helpers\OrderHelper;use Illuminate\Support\Facades\Request; @endphp
@php
    @endphp
<x-layouts.main>

    <div class="row">
        <div class="card col-xl-8 mb-3" id="ordersTable"
             data-list='{"valueNames":["order","date","address","status","amount"],"page":10,"pagination":true}'>
            <div class="card-header">
                <div class="row flex-between-center">
                    <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                        <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">Orders</h5>
                    </div>
                    <div class="col-8 col-sm-auto ms-auto text-end ps-0">
                        <div class="d-none" id="orders-bulk-actions">
                            <div class="d-flex"><select class="form-select form-select-sm" aria-label="Bulk actions">
                                    <option selected="">Bulk actions</option>
                                    <option value="Refund">Refund</option>
                                    <option value="Delete">Delete</option>
                                    <option value="Archive">Archive</option>
                                </select>
                                <button class="btn btn-falcon-default btn-sm ms-2" type="button">Apply</button>
                            </div>
                        </div>
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
            <div class="card-body p-0">
                <div class="table-responsive scrollbar">
                    <table class="table table-sm table-striped fs-10 mb-0 overflow-hidden">
                        <thead class="bg-200">
                        <tr>
                            <th>
                                <div class="form-check fs-9 mb-0 d-flex align-items-center"><input
                                        class="form-check-input"
                                        id="checkbox-bulk-customers-select"
                                        type="checkbox"
                                        data-bulk-select='{"body":"table-orders-body","actions":"orders-bulk-actions","replacedElement":"orders-actions"}'/>
                                </div>
                            </th>
                            <th class="text-900 sort pe-1 align-middle white-space-nowrap" data-sort="order">Order</th>
                            <th class="text-900 sort pe-1 align-middle white-space-nowrap pe-7" data-sort="date">Date
                            </th>

                            <th class="text-900 sort pe-1 align-middle white-space-nowrap text-center"
                                data-sort="status">
                                Status
                            </th>

                            <th class="no-sort"></th>
                        </tr>
                        </thead>
                        <tbody class="list" id="table-orders-body">
                        @foreach($orders as $order)
                            <tr class="btn-reveal-trigger">
                                <td class="align-middle" style="width: 28px;">
                                    <div class="form-check fs-9 mb-0 d-flex align-items-center">
                                        <label for="checkbox-29"></label><input class="form-check-input" type="checkbox"
                                                                                id="checkbox-29"
                                                                                data-bulk-select-row="data-bulk-select-row"/>
                                    </div>
                                </td>
                                <td class="order py-2 align-middle white-space-nowrap">
                                    <a href="{{route("order.show",$order->id)}}">
                                        <strong>#{{$order->id}}</strong>
                                        &nbsp<strong>{{$order->user?->full_name}}</strong>
                                        <br/>
                                    </a>

                                    <a href="tel:+{{$order->user?->phone}}">+{{$order->user?->phone}}</a>
                                </td>
                                <td class="date py-2 align-middle">{{$order->created_at->format("d.m.Y H:i")}}</td>

                                <td class="status py-2 align-middle text-center fs-9 white-space-nowrap"><span
                                        class="badge badge rounded-pill d-block badge-subtle-{{OrderHelper::getStatusColor($order->status)}}">{{__($order->status)}}<span
                                            class="ms-1 fas {{OrderHelper::getStatusIcon($order->status)}}"
                                            data-fa-transform="shrink-2"></span></span></td>
                                <td class="py-2 align-middle white-space-nowrap text-end">
                                    <x-order.status-action :order="$order"/>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
            <x-pagination :object="$orders"/>
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
                                <label class="mb-1">{{__("category")}}</label>
                                <select name="category_id" class="form-select form-select-sm">
                                    <option value="all">{{__("all")}}</option>
                                    @foreach($categories as $item)
                                        <option @if(Request::get("category_id") == $item->id) selected
                                                @endif value="{{$item->id}}">{{$item?->name}}</option>

                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 mt-n2">
                                <label class="mb-1">{{__("status")}}</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="all">{{__("all")}}</option>
                                    @foreach(OrderStatusEnum::toArray() as $item)
                                        <option @if(Request::get("status") == $item) selected
                                                @endif value="{{$item}}">{{__($item)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 mt-n2">
                                <label class="mb-1">{{__("phone")}}
                                    <input name="phone" type="text"
                                           value="{{Request::get("phone","998") != "" ? Request::get("phone","998") : "998"}}"
                                           class="form-control imask-phone">
                                </label>
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
