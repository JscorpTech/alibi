@props(['ext_footer',"ext_header"])
    <!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">


<!-- Mirrored from prium.github.io/falcon/v3.19.0/pages/authentication/card/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Dec 2023 15:31:27 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8"/><!-- /Added by HTTrack -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===============================================--><!--    Document Title-->
    <!-- ===============================================-->
    <title>Alibi Admin dashboard</title>

    <!-- ===============================================--><!--    Favicons-->
    <!-- ===============================================-->
    <link href="{{asset("output.css")}}" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="{{asset("assets/img/favicons/apple-touch-icon.png")}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset("assets/img/favicons/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset("assets/img/favicons/favicon-16x16.png")}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset("assets/img/favicons/favicon.ico")}}">
    <link rel="manifest" href="{{asset("assets/img/favicons/manifest.json")}}">
    <meta name="msapplication-TileImage" content="{{asset("assets/img/favicons/mstile-150x150.png")}}">
    <meta name="theme-color" content="#ffffff">
    <script src="{{asset("assets/js/config.js")}}"></script>
    <script src="{{asset("vendors/simplebar/simplebar.min.js")}}"></script>

    <!-- ===============================================--><!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.gstatic.com/">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap"
        rel="stylesheet">
    <link href="{{asset("vendors/dropzone/dropzone.min.css")}}" rel="stylesheet"/>

    <link href="{{asset("vendors/simplebar/simplebar.min.css")}}" rel="stylesheet">
    <link href="{{asset("assets/css/theme-rtl.min.css")}}" rel="stylesheet" id="style-rtl">
    <link href="{{asset("assets/css/theme.min.css")}}" rel="stylesheet" id="style-default">
    <link href="{{asset("assets/css/user-rtl.min.css")}}" rel="stylesheet" id="user-style-rtl">
    <link href="{{asset("assets/css/user.min.css")}}" rel="stylesheet" id="user-style-default">
    {{$ext_header ?? null}}
    @livewireStyles


</head>

<body x-data="{
    delete_modal:false,
    delete_url:'',
    banner:{},
    image_modal:{},

    delete_confirm:function(url){
        this.delete_modal = true;
        this.delete_url = url;
    }
}">
<div
    :class="delete_modal ? 'visible' : ''"
     class="invisible z-[999989] flex justify-center backdrop-blur-sm overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <button type="button" x-on:click="delete_modal = false"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="popup-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-4 md:p-5 text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{__("delete:confirm")}}</h3>
                <div class="flex justify-between">
                    <form :action="delete_url" method="post">
                        @method("delete")
                        @csrf
                        <button data-modal-hide="popup-modal" type="submit"
                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
                            {{__("yes")}}
                        </button>
                    </form>
                    <button x-on:click="delete_modal=false" type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-500 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">{{__("no")}}</button>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .loading {
        width: 100%;
        height: 100%;
        position: fixed;
        background-color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 99999999999999999999999999;
    }

    .loading img {
        max-width: 500px;
        max-height: 500px;
    }
</style>
{{--<div id="loading" class="loading">--}}
{{--    <img src="{{asset("assets/img/loading.gif")}}" alt="">--}}
{{--</div>--}}
<livewire:modal/>

<!-- ===============================================--><!--    Main Content-->
<!-- ===============================================-->
<div x-data="vars">
    {{$top ?? ''}}
    <main class="main" id="top">
        <div class="container-fluid">
            <div class="container" data-layout="container">

                <x-sidebar/>
                <div class="content">
                    <x-navbar/>
                    {{$slot}}
                    <x-footer/>
                </div>
            </div>
        </div>
    </main><!-- ===============================================--><!--    End of Main Content-->
    <!-- ===============================================-->
</div>
<x-thema/>
<!-- ===============================================--><!--    JavaScripts-->
<!-- ===============================================-->
<script src="{{asset("vendors/dropzone/dropzone.min.js")}}"></script>

<script src="{{asset("vendors/popper/popper.min.js")}}"></script>
<script src="{{asset("vendors/bootstrap/bootstrap.min.js")}}"></script>
<script src="{{asset("vendors/anchorjs/anchor.min.js")}}"></script>
<script src="{{asset("vendors/is/is.min.js")}}"></script>
<script src="{{asset("vendors/fontawesome/all.min.js")}}"></script>
<script src="{{asset("vendors/lodash/lodash.min.js")}}"></script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
<script src="{{asset("vendors/list.js/list.min.js")}}"></script>
<script src="{{asset("assets/js/theme.js")}}"></script>
{{$ext_footer ?? ""}}


<script>
    function onReady(callback) {
        var intervalId = window.setInterval(function () {
            if (document.getElementsByTagName('body')[0] !== undefined) {
                window.clearInterval(intervalId);
                callback.call(this);
            }
        }, 100);
    }

    function setVisible(selector, visible) {
        document.querySelector(selector).style.display = visible ? 'block' : 'none';
    }

    onReady(function () {
        setVisible('#loading', false);
    });
</script>
</body>

@livewireScripts
<!-- Mirrored from prium.github.io/falcon/v3.19.0/pages/authentication/card/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Dec 2023 15:31:27 GMT -->
</html>
