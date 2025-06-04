<div>
    <div x-data="{
        page:'register',
        password_show:false,
        password_show_reg:false,
        login_phone:'',
        login_password:'',
        register_phone:'',
        register_name:'',
        register_password:'',
        register_password_confirm:'',
        otp:{}

    }" x-init='
    document.addEventListener("edit-page", function (p) {
        $data.page = p.detail[0].page;
    });
'>

        <div class="login-container relative justify-center items-center flex h-[100vh]">
            <div class="login absolute bg-white p-5 max-w-[500px] !w-[100%] rounded-sm mt-auto">
                <div class="top">
                    <h1 class="text-center mb-[50px] font-bold text-[30px]">{{env("APP_NAME")}}</h1>
                </div>
                <div class="login-header gap-x-3 flex justify-around">
                    <div @click="page='login'"
                         class="cursor-pointer border-b-[1px] border-black w-full flex justify-center"
                         :class="page != 'login' ? 'opacity-50 border-[#3b3a3a]' :''">
                        <h1 class="uppercase mb-2 !font-[500] ">{{ __("login") }}</h1>
                    </div>
                    <div @click="page='register'"
                         class="cursor-pointer border-b-[1px] border-black w-full flex justify-center"
                         :class="page != 'register' ? 'opacity-50 border-[#3b3a3a]' :''">
                        <h1 class="uppercase mb-2 !font-[500]">{{ __("register") }}</h1>
                    </div>
                    <div class="border-b-[1px] border-black w-full flex justify-center"
                         :class="page != 'confirm' ? 'opacity-25 border-[#3b3a3a]' :''">
                        <h1 class="uppercase mb-2 !font-[500]">{{ __("confirm") }}</h1>
                    </div>
                </div>
                <div :class="page !== 'login' ? 'hidden' : ''" x-transition class="login-body mt-[40px] relative"
                     x-show="page=='login'" x-cloak>
                    <form x-on:submit.prevent="$wire.login(login_phone,login_password)">
                        @if($errors->login)
                            <div class="mb-5 bg-orange-100 border-l-4 border-red-500 text-orange-700 p-4" role="alert">
                                <p>{{$messages->login}}</p>
                            </div>
                        @endif

                        <div class="input-group flex flex-col">
                            <div class="input flex flex-col">
                                <label for="phone" class="text-[12px] font-[500]">{{__("phone")}} <span
                                        class="text-red-500">*</span></label>
                                <input x-model="login_phone" required value="998 "
                                       class="border-[1px] imask-phone placeholder:text-[13px] border-black p-1 mt-2"
                                       id="phone"
                                       type="text" name="phone" placeholder="{{ __("You phone number") }}">
                            </div>
                            <div class="input flex flex-col mt-5">
                                <label for="password" class="text-[12px] font-[500]">{{__("password")}} <span
                                        class="text-red-500">*</span></label>
                                <input x-model="login_password" required
                                       class="border-[1px] placeholder:text-[13px] border-black p-1 mt-2" id="phone"
                                       :type="password_show ? 'text' : 'password'" name="password"
                                       placeholder="{{ __("You password") }}">
                            </div>
                            <div class="input flex content-center mt-3">
                                <input @change="password_show = !password_show" id="show-password" type="checkbox">
                                &nbsp;
                                <label for="show-password">
                                    {{__("show:password")}}
                                </label>
                            </div>

                            <p class="opacity-50 text-[13px] mt-4">{{__("forgot:password")}}</p>
                            <button type="submit"
                                    class="p-2 mt-4 bg-black text-white">{{__("login")}}</button>

                        </div>

                    </form>
                </div>
                <div :class="page !== 'register' ? 'hidden' : ''" x-transition class="login-body mt-[40px] relative"
                     x-show="page=='register'" x-cloak>
                    <form
                        x-on:submit.prevent="$wire.register(register_phone,register_name,register_password,register_password_confirm)">
                        @if($errors->register)
                            <div class="mb-5 bg-orange-100 border-l-4 border-red-500 text-orange-700 p-4" role="alert">
                                <p>{{$messages->register}}</p>
                            </div>
                        @endif
                        <div class="input-group flex flex-col">
                            <div class="input flex flex-col mt-3">
                                <label for="phone" class="text-[12px] font-[500]">{{__("phone")}} <span
                                        class="text-red-500">*</span></label>
                                <input x-model="register_phone" required value="998"
                                       class="imask-phone border-[1px] placeholder:text-[13px] border-black p-1 mt-2"
                                       id="phone"
                                       type="text" name="phone" placeholder="{{ __("You phone number") }}">
                            </div>
                            <div class="input flex flex-col mt-3">
                                <label for="full_name" class="text-[12px] font-[500]">{{__("full_name")}} <span
                                        class="text-red-500">*</span></label>
                                <input x-model="register_name" required
                                       class="border-[1px] placeholder:text-[13px] border-black p-1 mt-2" id="full_name"
                                       type="text" name="full_name" placeholder="{{ __("You First name") }}">
                            </div>
                            <div class="input flex flex-col mt-3 relative">
                                <label for="password" class="text-[12px] font-[500]">{{__("password")}} <span
                                        class="text-red-500">*</span></label>
                                <input x-model="register_password" required
                                       class="border-[1px] placeholder:text-[13px] border-black p-1 mt-2" id="password"
                                       :type="password_show_reg ? 'text' : 'password'" name="password"
                                       placeholder="{{ __("You password") }}">
                            </div>
                            <div class="input flex flex-col mt-3">
                                <label for="password_confirm" class="text-[12px] font-[500]">{{__("password_confirm")}}
                                    <span
                                        class="text-red-500">*</span></label>
                                <input x-model="register_password_confirm" required
                                       class="border-[1px] placeholder:text-[13px] border-black p-1 mt-2"
                                       id="password_confirm"
                                       :type="password_show_reg ? 'text' : 'password'" name="password_confirm"
                                       placeholder="{{ __("You password confirm") }}">
                            </div>
                            <div class="flex justify-between">
                                <div class="input flex content-center mt-3">
                                    <input @change="password_show_reg = !password_show_reg" id="show-password-register"
                                           type="checkbox">
                                    &nbsp;
                                    <label for="show-password-register">
                                        {{__("show:password")}}
                                    </label>
                                </div>
                                <div class="input flex content-center mt-3">
                                    {{__("account:already")}}
                                    <strong class="cursor-pointer" @click="page='login'">
                                        &nbsp; {{ __("login") }}</strong>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="p-2 mt-4 bg-black text-white">{{__('register')}}</button>

                        </div>
                    </form>

                </div>
                <div :class="page !== 'confirm' ? 'hidden' : ''" x-transition class="login-body mt-[40px] relative"
                     x-show="page==='confirm'" x-cloak>

                    <form x-on:submit.prevent="$wire.confirm(otp)">
                        @if($errors->confirm)
                            <div class="mb-5 bg-orange-100 border-l-4 border-red-500 text-orange-700 p-4" role="alert">
                                <p>{{$messages->confirm}}</p>
                            </div>
                        @endif

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
                        <div class="input-group flex flex-col">
                            <div class="container justify-center flex">
                                <div class="input-field flex gap-4">
                                    <input x-model="otp.n1" required class="otp-input" type="number"/>
                                    <input x-model="otp.n2" required class="otp-input" type="number" disabled/>
                                    <input x-model="otp.n3" required class="otp-input" type="number" disabled/>
                                    <input x-model="otp.n4" required class="otp-input" type="number" disabled/>
                                </div>
                            </div>
                            <div class="input flex content-center justify-center mt-5">
                                <p class="cursor-pointer" @click="$wire.resend()" id="resend">{{__("resend")}}</p>
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

                            <p class="opacity-50 text-[13px] text-center mt-4">{{__("forgot:password")}}</p>
                            <button
                                type="submit"
                                class="p-2 mt-4 bg-black text-white">{{__("confirm")}}</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>


    <x-ext.imask/>
</div>
