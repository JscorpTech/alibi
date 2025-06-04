<div>

@if($is_open ?? false)
        <div class="modal fade  d-block opacity-100 backdrop-filter backdrop-blur-sm" style="transition: 2s">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 800px">
                <div class="modal-content position-relative">
                    <div class="position-absolute top-0 end-0 mt-2 me-2 z-1">
                        <button wire:click="$set('is_open',false)"
                                class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">

                        <div class="p-4 pb-5 pt-5">
                            @if($product != null)
                                <x-com.product.card :product="$product"/>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>


