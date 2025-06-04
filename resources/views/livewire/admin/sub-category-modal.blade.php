<div>
    <div x-show="modal.open" x-cloak
         class="fixed justify-center flex backdrop-blur-sm z-[99999] items-center w-[100vw] h-[100vh]">
        <div
            class="overflow-y-auto flex overflow-x-hidden top-0 right-0 left-0 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow">
                    <div
                        class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{__("subcategory")}}
                        </h3>
                        <button x-on:click="toggle" type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center "
                                data-modal-toggle="crud-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <form x-on:submit.prevent="$wire.submit(subcategory,modal.type)"
                          class="p-4 md:p-5">
                        <div class="grid gap-4 mb-4 grid-cols-2">
                            <div class="col-span-2">
                                <label for="name"
                                       class="block mb-2 text-sm font-medium text-gray-900 ">{{__("name")}}</label>
                                <input x-model="subcategory.name" type="text" name="name" id="name"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                       placeholder="{{__("subcategory:name")}}" required="">
                            </div>
                            <div class="col-span-2">
                                <label for="name"
                                       class="block mb-2 text-sm font-medium text-gray-900 ">{{__("code")}}</label>
                                <input min="1" x-model="subcategory.code" type="number" name="name" id="name"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 "
                                       placeholder="{{__("subcategory:code")}}" required="">
                            </div>

                            <div class="col-span-2">
                                <label for="name"
                                       class="block mb-2 text-sm font-medium text-gray-900 ">{{__("position")}}</label>
                                <input min="1" x-model="subcategory.position" type="number" name="name" id="name"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 "
                                       placeholder="{{__("subcategory:position")}}" required="">
                            </div>
                            <div class="col-span-2">
                                <label for="category"
                                       class="block mb-2 text-sm font-medium text-gray-900 ">{{__("category")}}</label>
                                <select x-model="subcategory.category" id="category"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-span-2">
                                <label for="gender"
                                       class="block mb-2 text-sm font-medium text-gray-900 ">{{__("gender")}}</label>
                                <select x-model="subcategory.gender" id="gender"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                    @foreach($genders as $gender)
                                        <option value="{{$gender}}">{{ __($gender) }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                          d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                          clip-rule="evenodd"></path>
                                </svg>
                                {{__("save")}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
