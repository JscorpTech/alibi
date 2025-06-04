@php use App\Models\Settings; @endphp
<div>
    <div
        class="mt-20 shadow-lg p-4 rounded bg-white w-[400px] text-center border-t-4 border-red-600 !max-w-[90%] mx-auto text-gray-700 pt-0">
            <span class="bg-red-600 text-white p-4 rounded-full inline-flex -mt-8 mb-2">
              <svg class="fill-current w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path
                      d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/></svg>
    </span>
        <h3 class="font-bold text-2xl text-black mb-2">{{ __("Deleted Account ✅") }}</h3>
        <ul class="list-disc inline-block text-center pt-2 ml-2">
            {!! strip_tags(Settings::get("account:deleted")) !!}
        </ul>
        <div class="flex pt-8">
            <a
                href="/"
                class="w-full mr-1 bg-white border text-gray-600 border-gray-400 hover:bg-gray-300 py-2 px-4 rounded font-medium">
                {{ __("Home") }}
            </a>
        </div>
    </div>
</div>
