<div x-show="modal.open" x-cloak="" class="w-[100vw] justify-center items-center flex h-[100vh] z-[99999] fixed backdrop-blur-sm">
    <style>
        .scroll-hidden::-webkit-scrollbar{
            display: none;
        }
    </style>
    <div class="relative scroll-hidden p-4 overflow-scroll w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <button type="button" x-on:click="modal.toggle()"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="popup-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-4 md:p-5 pt-5">
                {{$slot}}
            </div>
        </div>
    </div>

</div>
