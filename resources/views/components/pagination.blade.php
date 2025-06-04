@props([
    "object"
])
<div class="card-footer bg-body-tertiary d-flex justify-content-center">
    @if($object->lastPage() != 1)
        <div>
            @if($object->currentPage() == 1)
                <a class="btn opacity-50 btn-falcon-default btn-sm me-2" type="button" disabled="disabled"
                   data-bs-toggle="tooltip" data-bs-placement="top" title="Prev"><span
                        class="fas fa-chevron-left"></span></a>
            @else
                <a href="{{$object->previousPageUrl()}}" class="btn btn-falcon-default btn-sm me-2" type="button"
                   disabled="disabled"
                   data-bs-toggle="tooltip" data-bs-placement="top" title="Prev"><span
                        class="fas fa-chevron-left"></span></a>
            @endif
            @if($object->currentPage() == $object->lastPage() and $object->currentPage() != 1)
                <a class="btn btn-sm btn-falcon-default me-2"
                   href="{{$object->url($object->currentPage()-1)}}">{{$object->currentPage()-1}}</a>
            @endif
            <a class="btn btn-sm btn-falcon-default text-primary me-2" href="#!">{{$object->currentPage()}}</a>
            @if($object->currentPage() != $object->lastPage())
                <a class="btn btn-sm btn-falcon-default me-2"
                   href="{{$object->url($object->currentPage()+1)}}">{{$object->currentPage()+1}}</a>
            @endif
            <a class="btn btn-sm btn-falcon-default me-2" href="#!"> <span class="fas fa-ellipsis-h"></span></a>
            <a class="btn btn-sm btn-falcon-default me-2"
               href="{{$object->url($object->lastPage())}}">{{$object->lastPage()}}</a>
            @if(!$object->hasMorePages())
                <a class="btn opacity-50 btn-falcon-default btn-sm" type="button"
                   data-bs-toggle="tooltip"
                   data-bs-placement="top" title="Next"><span class="fas fa-chevron-right">     </span></a>
            @else
                <a href="{{$object->nextPageUrl()}}" class="btn btn-falcon-default btn-sm" type="button"
                   data-bs-toggle="tooltip"
                   data-bs-placement="top" title="Next"><span class="fas fa-chevron-right">     </span></a>
            @endif
        </div>

    @endif
</div>
