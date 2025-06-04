<x-layouts.auth>
    <div class="row min-vh-100 flex-center g-0">
        <div class="col-lg-8 col-xxl-5 py-3 position-relative"><img class="bg-auth-circle-shape"
                                                                    src="{{asset("assets/img/icons/spot-illustrations/bg-shape.png")}}"
                                                                    alt="" width="250"><img
                    class="bg-auth-circle-shape-2" src="{{asset("assets/img/icons/spot-illustrations/shape-1.png")}}"
                    alt="" width="150">
            <div class="card overflow-hidden z-1">
                <div class="card-body p-0">
                    <div class="row g-0 h-100">
                        <div class="col-md-5 text-center bg-card-gradient">
                            <div class="position-relative p-4 pt-md-5 pb-md-7" data-bs-theme="light">
                                <div class="bg-holder bg-auth-card-shape"
                                     style="background-image:url({{asset("assets/img/icons/spot-illustrations/half-circle.png")}});"></div>
                                <!--/.bg-holder-->
                                <div class="z-1 position-relative"><a
                                            class="link-light mb-4 font-sans-serif fs-5 d-inline-block fw-bolder"
                                            href="#">{{env("APP_NAME")}}</a>
                                    <p class="opacity-75 text-white">{{__("login:desc")}}</p>
                                </div>
                            </div>
                            <div class="mt-3 mb-4 mt-md-4 mb-md-5" data-bs-theme="light">
                                <p class="text-white"><br><a
                                            class="text-decoration-underline link-light" href="register.html"></a></p>
                                <p class="mb-0 mt-4 mt-md-5 fs-10 fw-semi-bold text-white opacity-75">Read our <a
                                            class="text-decoration-underline text-white" href="#!">terms</a> and <a
                                            class="text-decoration-underline text-white" href="#!">conditions </a></p>
                            </div>
                        </div>
                        <div class="col-md-7 d-flex flex-center">
                            <div class="p-4 p-md-5 flex-grow-1">
                                <div class="row flex-between-center">
                                    <div class="col-auto">
                                        <h3>{{__("account:login")}}</h3>
                                    </div>
                                </div>
                                <form action="{{route("login")}}" method="post"
                                      class="needs-validation">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="validationServer03"
                                               class="form-label">{{__("phone:number")}}</label>
                                        <input placeholder="998 (94) 399-05-09" value="{{old("phone","998 ")}}" type="text" name="phone"
                                               class="form-control imask-phone @if($errors->has("phone")) is-invalid @elseif($errors->any()) is-valid @endif"
                                               id="validationServer03"
                                               aria-describedby="validationServer03Feedback" required>
                                        @error("phone")
                                        <div id="validationServer03Feedback" class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror()
                                    </div>

                                    <div class="mb-3">
                                        <label for="validationServer03" class="form-label">{{__("password")}}</label>
                                        <input placeholder="You password" name="password" type="password"
                                               class="form-control @if($errors->has("phone")) is-invalid @elseif($errors->any()) is-valid @endif"
                                               id="validationServer03"
                                               aria-describedby="validationServer03Feedback" required>
                                        @error("password")
                                        <div id="validationServer03Feedback" class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror()
                                    </div>


                                    <div class="row flex-between-center">
                                        <div class="col-auto">
                                            <div class="form-check mb-0">
                                                <input name="remember" value="true"
                                                       class="form-check-input"
                                                       type="checkbox"
                                                       id="card-checkbox"
                                                       checked="checked"
                                                />
                                                <label
                                                        class="form-check-label mb-0"
                                                        for="card-checkbox">{{__("remember:me")}}</label></div>
                                        </div>
{{--                                        <div class="col-auto"><a class="fs-10"--}}
{{--                                                                 href="forgot-password.html">{{__("forgot:password")}}--}}
{{--                                                ?</a>--}}
{{--                                        </div>--}}
                                    </div>
                                    <div class="mb-3">
                                        <button class="btn btn-primary d-block w-100 mt-3" type="submit"
                                                name="submit">{{__("login")}}</button>
                                    </div>
                                </form>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-ext.imask/>
</x-layouts.auth>
