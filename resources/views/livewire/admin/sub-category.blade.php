@php use App\Enums\GenderEnum; @endphp
<div>
    <x-slot:top>
        <livewire:admin.sub-category-modal :categories="$categories"/>
        <livewire:admin.sub-category-delete/>
    </x-slot:top>

    <div
        class="block flex justify-end p-2 mb-3 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 ">
        <button
            x-on:click="toggle;modal.type='create';subcategory.name='';subcategory.code=1;subcategory.gender='{{GenderEnum::MALE}}';subcategory.category={{$categories[0]->id}}"
            type="button"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 me-2 mb-2  focus:outline-none ">{{__("create")}}</button>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
            <thead
                class="text-xs text-gray-700 uppercase bg-gray-50 ">
            <tr>
                <th scope="col" class="px-6 py-3">
                    {{__("name")}}
                </th>
                <th scope="col" class="px-6 py-3">
                    {{__("category")}}
                </th>
                <th scope="col" class="px-6 py-3">
                    {{__("code")}}
                </th>
                <th scope="col" class="px-6 py-3">
                    {{__("gender")}}
                </th>
                <th scope="col" class="px-6 py-3">
                    {{__("position")}}
                </th>
                <th scope="col" class="px-6 py-3 text-right">
                    {{__("action")}}
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $item)
                <tr class="bg-white border-b  hover:bg-gray-50 ">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                        {{$item->name}}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                        {{$item->category->name}}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                        {{$item->code}}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                        {{ __($item->gender) }}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                        {{$item->position}}
                    </th>
                    <td class="px-6 py-4 text-right flex justify-end">
                        <div
                            x-on:click="subcategory.name=`{{$item->name}}`;subcategory.code=`{{$item->code}}`;subcategory.position=`{{$item->position}}`;subcategory.gender='{{$item->gender}}';subcategory.id={{$item->id}};subcategory.category={{$item->category->id}};modal.type='edit';modal.open=true;"
                            class="mr-2 cursor-pointer font-medium text text-[orange] hover:underline">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div x-on:click="deleteModal.open=true;deleteModal.id = {{$item->id}}"
                             class="ml-2 cursor-pointer font-medium text-[red] hover:underline">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

<script>
    document.addEventListener("alpine:init", function () {
        Alpine.data("vars", () => ({
            modal: {
                open: false,
                type: "create",
            },
            deleteModal: {
                open: false
            },
            subcategory: {
                name: '',
                items: [],
                category: {{$categories[0]->id}}
            },
            toggle() {
                this.modal.open = !this.modal.open
            },
            init() {
                window.addEventListener("closeModal", () => {
                    this.modal.open = false;
                    this.deleteModal.open = false;
                });
            }
        }));
    })

</script>
