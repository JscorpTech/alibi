@php use App\Models\Settings; @endphp
@props([
    "error"
])
<div x-data="vars">
    <div
        class="mt-20 shadow-lg p-4 rounded bg-white w-[400px] text-center border-t-4 border-red-600 !max-w-[90%] mx-auto text-gray-700 pt-0">
            <span class="bg-red-600 text-white p-4 rounded-full inline-flex -mt-8 mb-2">
              <svg class="fill-current w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path
                      d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/></svg>
    </span>
        <h3 class="font-bold text-2xl text-black mb-2">{{ __("Enter confirm code?") }}</h3>
        @error("error")
        <p class="text-red-500">{{ $message }}</p>
        @enderror()
        <style>
            /* Import Google font - Poppins */
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap");

            .input-field input {
                height: 60px;
                width: 60px;
                border-radius: 6px;
                outline: none;
                font-size: 1.125rem;
                text-align: center;
                border: 1px solid #ddd;
            }

            .input-field input:focus {
                box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
            }

            .input-field input::-webkit-inner-spin-button,
            .input-field input::-webkit-outer-spin-button {
                display: none;
            }
        </style>
        <form action="" x-on:submit.prevent="$wire.confirm(otp)">
            <div class="input-group flex flex-col">
                <div class="container mt-5 justify-center flex">
                    <div class="input-field flex gap-4">
                        <input x-model="otp.n1" required class="otp-input" type="number"/>
                        <input x-model="otp.n2" required class="otp-input" type="number" disabled/>
                        <input x-model="otp.n3" required class="otp-input" type="number" disabled/>
                        <input x-model="otp.n4" required class="otp-input" type="number" disabled/>
                    </div>
                </div>
                <div class="input flex content-center justify-center mt-5">
                </div>

                <script>
                    const inputs = document.querySelectorAll(".otp-input"),
                        button = document.querySelector("button");

                    inputs.forEach((input, index1) => {
                        input.addEventListener("keyup", (e) => {
                            // This code gets the current input element and stores it in the currentInput variable
                            // This code gets the next sibling element of the current input element and stores it in the nextInput variable
                            // This code gets the previous sibling element of the current input element and stores it in the prevInput variable
                            const currentInput = input,
                                nextInput = input.nextElementSibling,
                                prevInput = input.previousElementSibling;

                            // if the value has more than one character then clear it
                            if (currentInput.value.length > 1) {
                                currentInput.value = "";
                                return;
                            }
                            // if the next input is disabled and the current value is not empty
                            //  enable the next input and focus on it
                            if (nextInput && nextInput.hasAttribute("disabled") && currentInput.value !== "") {
                                nextInput.removeAttribute("disabled");
                                nextInput.focus();
                            }

                            // if the backspace key is pressed
                            if (e.key === "Backspace") {
                                // iterate over all inputs again
                                inputs.forEach((input, index2) => {
                                    // if the index1 of the current input is less than or equal to the index2 of the input in the outer loop
                                    // and the previous element exists, set the disabled attribute on the input and focus on the previous element
                                    if (index1 <= index2 && prevInput) {
                                        input.setAttribute("disabled", true);
                                        input.value = "";
                                        prevInput.focus();
                                    }
                                });
                            }
                            //if the fourth input( which index number is 3) is not empty and has not disable attribute then
                            //add active class if not then remove the active class.
                            if (!inputs[3].disabled && inputs[3].value !== "") {
                                button.classList.add("active");
                                return;
                            }
                            button.classList.remove("active");
                        });
                    });

                    window.addEventListener("load", () => inputs[0].focus());
                </script>
                <div class="flex pt-8">
                    <a
                        href="{{ route("delete-account:phone") }}"
                        class="w-1/2 mr-1 bg-white border text-gray-600 border-gray-400 hover:bg-gray-300 py-2 px-4 rounded font-medium">
                        {{ __("Back") }}
                    </a>
                    <button
                        type="submit"
                        class="w-1/2 transition ml-1 bg-black border border-black text-white hover:bg-red-700 hover:border-red-700 py-2 px-4 rounded font-medium">
                        {{ __("Delete") }}
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        window.addEventListener("alpine:init", () => {
            Alpine.data("vars", () => ({
                otp: {}
            }))
        })
    </script>
</div>
