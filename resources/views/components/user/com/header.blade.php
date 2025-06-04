<div
    id="header_wrapper"
    class="sticky w-full top-0 z-50 header-el | transition-[opacity] duration-700 ease-[ease] opacity-0"
    :class="mobileMenu !== false ? '!translate-y-0' : ''"
    x-data="
        {
          headerheight(){
            let header = document.querySelector('header')

            let topBarheight = 0
            if (document.querySelector('#message-bar')) {
              topBarheight = document.querySelector('#message-bar').offsetHeight
            }

            let headerheight = header.offsetHeight;

            let topBarCountdownheight = 0
            if (document.querySelector('#topbar-countdown') && !document.querySelector('#topbar-countdown').classList.contains('hidden')) {
              topBarCountdownheight = document.querySelector('#topbar-countdown').offsetHeight;
              headerheight = headerheight + topBarCountdownheight;
            }

            document.body.style.setProperty('--header-height', headerheight + 'px');
            document.body.style.setProperty('--top-bar-height', topBarheight + 'px');
            document.body.style.setProperty('--top-bar-countdown-height', topBarCountdownheight + 'px');
            if (location.pathname === '/search') {
              searchOpen = true
            }
            const realVh = document.querySelector('.realVH').offsetHeight
            document.body.style.setProperty('--real-viewport', realVh + 'px');
            const pdpSlider = document.querySelector('.product-template--slider')
            if (pdpSlider) document.body.style.setProperty('--product-slider-height', pdpSlider.offsetHeight + 'px');
          },
          OpenDropdownMenu(hendle, menu_item){
            let dropdown =  document.getElementById(hendle)
            let menuDesktopItem = document.getElementById(menu_item)
            menuDesktopItem.append(dropdown)
          },
          mobileMenu:false
        }
      "
>


    <div id="shopify-section-top-bar" class="shopify-section">
        <link async rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"/>
        <div
            x-data="{showTopBar: false}"
            x-show="showTopBar"
            class="top-bar hidden"
            style="background:#000; color: #fff;">
            <div class="w-full flex items-center px-4 sm:px-6">
                <div id="payment-options" class="md:w-[95%] px-[0.75rem] relative z-0">

                </div>

                <div class="md:w-[5%] flex justify-end px-[0.25rem]" x-data="{
                                                            navMoove(){
                                                                 let headerheight = document.querySelector('header').offsetHeight;
                                                                 document.body.style.setProperty('--header-height', headerheight + 'px');
                                                            }
                                                             }">


                    <button aria-label="Close" class="closeTopBar ml-4" @click="showTopBar = false
                                                                        navMoove()">

                        <svg
                            width="18"
                            height="17"
                            viewBox="0 0 18 17"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg" role="presentation">
                            <line
                                y1="-0.5"
                                x2="22.6267"
                                y2="-0.5"
                                transform="matrix(0.70713 -0.707084 0.70713 0.707084 1 17)"
                                stroke="black"/>
                            <line
                                y1="-0.5"
                                x2="22.6267"
                                y2="-0.5"
                                transform="matrix(0.70713 0.707084 -0.70713 0.707084 1 1)"
                                stroke="black"/>
                        </svg>


                    </button>
                </div>
            </div>
        </div>

        <style>
            .top-bar .swiper-slide {
                text-align: center;
                display: flex;
                justify-content: center;
            }

            .arrow-down {
                margin-top: 1px;
                margin-left: 5px;
            }

            .closeTopBar line {
                stroke: white;
            }

            .closeTopBar svg {
                width: 9px;
            }
        </style>

        <script>
            function countryPopup() {
                document.getElementById("site-selector-modal-country").style.display = "flex";
            }

            function closeMobileMenu() {
                var m = document.getElementById("mainMobMenu__backdrop");
                if (m.classList.contains("opacity-1")) {
                    m.click();
                }
            }
        </script>
    </div>
    <div id="shopify-section-top-bar-countdown" class="shopify-section">
        <div id="topbar-countdown"
             class=" hidden  relative text-center uppercase grid items-center text-[11px] min-h-[24px] py-1 px-8">
            <div class="topbar-countdown__msg w-full whitespace-nowrap">
                <a href="https://representclo.com/collections/black-friday-sale"
                   class="hover:opacity-7 flex items-center justify-center">
                    EXTRA +15% OFF BLACK FRIDAY SALE WITH CODE: BF15


                </a>

            </div>
            <button aria-label="Close"
                    class="absolute top-1/2 right-[0px] p-[10px] -translate-y-1/2 scale-75 | md:hidden">

                <svg
                    width="12"
                    height="12"
                    viewBox="0 0 18 17"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg" role="presentation">
                    <line
                        y1="-0.5"
                        x2="22.6267"
                        y2="-0.5"
                        transform="matrix(0.70713 -0.707084 0.70713 0.707084 1 17)"
                        stroke="#ffffff"/>
                    <line
                        y1="-0.5"
                        x2="22.6267"
                        y2="-0.5"
                        transform="matrix(0.70713 0.707084 -0.70713 0.707084 1 1)"
                        stroke="#ffffff"/>
                </svg>


            </button>
            <button aria-label="Close"
                    class="absolute top-1/2 right-[0px] p-[10px] -translate-y-1/2 scale-75 | hidden md:block">

                <svg
                    width="12"
                    height="12"
                    viewBox="0 0 18 17"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg" role="presentation">
                    <line
                        y1="-0.5"
                        x2="22.6267"
                        y2="-0.5"
                        transform="matrix(0.70713 -0.707084 0.70713 0.707084 1 17)"
                        stroke="#ffffff"/>
                    <line
                        y1="-0.5"
                        x2="22.6267"
                        y2="-0.5"
                        transform="matrix(0.70713 0.707084 -0.70713 0.707084 1 1)"
                        stroke="#ffffff"/>
                </svg>


            </button>
        </div>

        <style>


            #topbar-countdown {
                background: #0d0e0e;
                font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            }

            #topbar-countdown .topbar-countdown__msg {
                background: #0d0e0e;
                color: #ffffff;
                grid-row: 1;
                grid-column: 1;
            }

            .equals_times {
                transition: ease all .3s !important;
                background-color: transparent;
            }

            .equals_times.active {
                background-color: #FFFFFF;
            }

            @media (max-width: 767px) {
                #topbar-countdown {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
                }

                #topbar-countdown .topbar-countdown__msg {
                    grid-row: 1;
                    grid-column: 1;
                }
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let displayMessageBar = true

                function removeTopbar() {
                    document.getElementById('topbar-countdown').remove();
                    displayMessageBar = false
                    cookies.setCookie('topbar_countdown', 'false', 1)
                    const topBar = document.querySelector('#header')
                    document.body.style.setProperty('--top-bar-countdown-height', '0px');

                    let headerheight = header.offsetHeight;
                    document.body.style.setProperty('--header-height', headerheight + 'px');
                }

                if (cookies.getCookie('topbar_countdown')) {
                    removeTopbar();
                }
                console.log(displayMessageBar)
                if (displayMessageBar) {
                    const closeButtons = document.querySelectorAll('#topbar-countdown button');

                    closeButtons.forEach((button) => {
                        button.addEventListener('click', function (event) {
                            removeTopbar();
                        });
                    });
                }

            })
        </script>

    </div>
    <div id="shopify-section-main-header" class="shopify-section">


        <header id="header" class="hide-massage-bar index" x-init="headerheight()" x-data="{timeout: null}">
            <div
                x-cloak
                class="bg-black bg-opacity-20 fixed top-0 left-0 h-[100vh] w-full z-[-2] transition-all duration-700 ease-[ease]"
                :class="renderDesktopMenuBackdrop ? 'opacity-1 visible' : 'opacity-0 invisible'"
                style="-webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px);"></div>

            <div class="top-0" x-data="{
    toggleState($event){
      $event.preventDefault();

      this.searchOpen = !this.searchOpen
      const sBox = document.querySelector('#searchbox input')
      if (sBox){
        setTimeout(function() { sBox.focus() }, 20);

        }
      if (this.searchOpen) {
        document.querySelector('body').classList.add('search_open')
        const firstL = document.querySelector('#firstLottie')
        if (firstL) {
          firstL.classList.add('hidden')
        }
        const secondL = document.querySelector('#secondLottie')
        if (secondL) {
          secondL.classList.remove('hidden')
        }
        window.scroll({
            left: 0,
            top: 0
        });
      } else {
        document.querySelector('body').classList.remove('search_open')
        const secondL = document.querySelector('#secondLottie')
        if (secondL) {
          secondL.classList.add('hidden')
        }
        const firstL = document.querySelector('#firstLottie')
        if (firstL) {
          firstL.classList.remove('hidden')
        }

      }
      }
    }">
                <div id="main-header-wrap" class="mx-auto px-4 sm:px-6 lg:hover:bg-white">
                    <div
                        class="flex-row-reverse lg:flex-row xs:justify-between mx-auto flex justify-between lg:justify-center items-center xs:justify-start lg:items-stretch">
                        <div class="order-[5]  flex flex-row-reverse">
                            <button aria-label="Search" class="lg:order-last xs:order-first lg:hidden p-2 inline-flex items-center justify-center focus:outline-none algolia-search js-alg-trigger" @click="toggleState($event)">

                                <svg class="search" width="32" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg" role="presentation">
                                    <circle cx="6.21825" cy="7.05418" r="5.71825" stroke="black"></circle>
                                    <path d="M14.9226 17.0027L9.32617 11.4062" stroke="black"></path>
                                </svg>



                            </button>
                            <button
                                x-on:click="
                                    console.log('render');
                                    if(mobileMenu){
                                        mobileMenu = false;
                                    }else{
                                        mobileMenu = true;
                                    }; noScroll = !noScroll;"
                                type="button"
                                class="lg:order-last xs:order-first lg:hidden inline-flex items-center justify-center focus:outline-none sm:-ml-[17px] equals_times"
                                aria-label="Open/Close Menu" :class="mobileMenu !== false ? 'active ' : ''"
                                :aria-expanded="mobileMenu !== false ? 'true' : 'false'"></button>
                        </div>
                        <nav class="hidden lg:flex flex-0" aria-labelledby="mainmenulabel">
                            <span id="mainmenulabel" class="sr-only">Main Menu</span>
                            <div class="md:hidden">
                                <button
                                    x-on:click="
                                    console.log('render');
                                    if(mobileMenu){
                                        mobileMenu = false;
                                    }else{
                                        mobileMenu = true;
                                    }; noScroll = !noScroll;"
                                    type="button"
                                    class="lg:order-last xs:order-first  inline-flex items-center justify-center focus:outline-none sm:-ml-[17px] equals_times"
                                    aria-label="Open/Close Menu" :class="mobileMenu !== false ? 'active ' : ''"
                                    :aria-expanded="mobileMenu !== false ? 'true' : 'false'"></button>
                            </div>
                            <div class="md:flex items-center hidden">
                                <div class="categories flex gap-4">
                                    @foreach($categories as $category)
                                        <p style="white-space: nowrap"
                                           @mouseover="MyHeader.show(`{{$category->name}}`,{{json_encode($category->subcategory()->orderBy("position")->get())}},{{$category->id}})"
                                           class="@if($current == $category->id) font-bold @endif cursor-pointer text-[13px]">
                                            <a
                                                    href="{{route("category",['type'=>"category","id"=>$category->id])}}">{{$category->name}}</a>
                                        </p>
                                    @endforeach
                                </div>
                            </div>
                            </p>
                        </nav>
                        <div class="flex justify-center order-[2] lg:order-[0] lg:flex">

                            <a href="/">
                                <span class="sr-only">REPRESENT CLO | US</span>

                                <div class="relative">
                                    <div id="firstLottie" class="flex items-center justify-center"
                                         style="width: 266px; height: 70px;">
                                        <p class="font-[Poppins] text-[40px]">alibi</p>
                                    </div>
                                    <div id="secondLottie" class="flex items-center justify-center secondLottie hidden"
                                         style="width: 266px; height: 70px;">
                                        <p class="font-[Poppins] text-[40px]">alibi</p>
                                    </div>

                                    <style>
                                        .secondLottie {
                                            width: 266px;
                                            height: 70px;
                                        }

                                        @media screen and (max-width: 767px) {
                                            #firstLottie,
                                            .secondLottie {
                                                width: 100% !important;
                                                max-width: 220px !important;
                                                height: 70px !important;
                                            }
                                        }
                                    </style>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            const firstL = document.querySelector('#firstLottie')
                                            const secondL = document.querySelector('#secondLottie')
                                            window.addEventListener('scroll', function () {
                                                if (window.scrollY > 0) {
                                                    if (location.pathname.includes('/products/')) {
                                                        document.querySelector('#main-header-wrap').classList.add('bg-white');
                                                    }
                                                    if (firstL) {
                                                        firstL.classList.add('hidden');
                                                    }
                                                    if (secondL) {
                                                        secondL.classList.remove('hidden');
                                                    }
                                                    // Check template and request.page_type here and modify the document.documentElement.classList accordingly


                                                } else {
                                                    if (location.pathname.includes('/products/')) {
                                                        document.querySelector('#main-header-wrap').classList.remove('bg-white');
                                                    }
                                                    if (secondL) {
                                                        secondL.classList.add('hidden');
                                                    }
                                                    if (firstL) {
                                                        firstL.classList.remove('hidden');
                                                    }
                                                    // Check template and request.page_type here and modify the document.documentElement.classList accordingly


                                                }
                                            });
                                        })
                                    </script>

                                </div>

                            </a>

                        </div>
                        <div class="flex order-[1] justify-end lg:flex-0 items-stretch">

                            <button aria-label="Search" class="hidden lg:flex items-center px-[0.25rem] cursor-pointer"
                                    class="algolia-search js-alg-trigger"
                                    @click="toggleState($event)">

                                <svg
                                        class="search"
                                        width="32"
                                        height="18"
                                        viewBox="0 0 16 18"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg" role="presentation">
                                    <circle
                                            cx="6.21825"
                                            cy="7.05418"
                                            r="5.71825"
                                            stroke="black"/>
                                    <path d="M14.9226 17.0027L9.32617 11.4062" stroke="black"/>
                                </svg>


                            </button>


                            @can("auth")
                                <div class="relative lg:flex items-center !p-[0] account-menu-wrapper">
                                    <svg
                                            class="account "
                                            width="32"
                                            height="32"
                                            viewBox="0 0 32 32"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg" role="presentation">
                                        <circle
                                                cx="16"
                                                cy="16"
                                                r="16"/>
                                        <path
                                                d="M9 23C9 23 10.337 21.4847 11.4843 21C13.1107 20.3129 14.2257 20.5004 16 20.5C17.7779 20.4996 18.8989 20.3134 20.525 21C21.6665 21.4819 23 22.9854 23 22.9854"
                                                stroke="black"
                                                stroke-miterlimit="10"
                                                stroke-linecap="square"/>
                                        <path
                                                d="M12 14.1429C12 16.7143 14.4444 18 16.0159 18C17.5873 18 20.0318 16.7143 20.0318 14.1429V12.4286C20.0318 11 18.8095 9 16.0159 9C13.2222 9 12 11 12 12.4286V14.1429Z"
                                                stroke="black"
                                                stroke-width="0.830323"/>
                                    </svg>
                                    <span class="sr-only">TODO</span>
                                    <a href="{{route("cabinet")}}"
                                       title="account link"
                                       class="header__icon block text-center | absolute top-0 right-0 bottom-0 left-0"><span
                                                class="sr-only">Account</span></a>

                                </div>
                                &nbsp;
                            @else
                                <a href="{{route("auth")}}" class=""
                                   style="display: flex;justify-content: center;align-content: center;align-items: center">
                                    <svg
                                            class="account "
                                            width="32"
                                            height="32"
                                            viewBox="0 0 32 32"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg" role="presentation">
                                        <circle
                                                cx="16"
                                                cy="16"
                                                r="16"/>
                                        <path
                                                d="M9 23C9 23 10.337 21.4847 11.4843 21C13.1107 20.3129 14.2257 20.5004 16 20.5C17.7779 20.4996 18.8989 20.3134 20.525 21C21.6665 21.4819 23 22.9854 23 22.9854"
                                                stroke="black"
                                                stroke-miterlimit="10"
                                                stroke-linecap="square"/>
                                        <path
                                                d="M12 14.1429C12 16.7143 14.4444 18 16.0159 18C17.5873 18 20.0318 16.7143 20.0318 14.1429V12.4286C20.0318 11 18.8095 9 16.0159 9C13.2222 9 12 11 12 12.4286V14.1429Z"
                                                stroke="black"
                                                stroke-width="0.830323"/>
                                    </svg>
                                </a>
                            @endcan

                            <a class="flex" href="{{route("cabinet")}}">
                                <button

                                        class="relative p-[0.25rem] btn-cart"
                                        style="display: flex;justify-content: center;align-items: center"
                                        aria-label="Cart Toggle"
                                        role="button"
                                >


                                    <svg class="cart" width="32" height="16" viewBox="0 0 13 16" fill="none"
                                         xmlns="http://www.w3.org/2000/svg" role="presentation">
                                        <path d="M0.5 15.5V5.00025L11 5.00025L12.3001 5.00013L12.5 5.00009V15.5H0.5Z"
                                              stroke="black" stroke-linejoin="round"></path>
                                        <path d="M3.50028 5C3.50028 5 3.00024 1 6.50024 1C10.0003 1 9.50023 5 9.50023 5"
                                              stroke="black"></path>
                                    </svg>

                                    <span
                                            id="cart_buble"
                                            class="w-[11px] h-[11px] hidden bg-primary-dark text-white rounded-full absolute top-[13px] right-[6px] text-[8px]">0</span>
                                </button>
                            </a>

                        </div>
                    </div>
                </div>
        </header>

        <script>

            window.messageDuration = 5;


            window.countdownCutoff = false;


            window.saleCountdownCutoff = false;

        </script>

        <style>
            /* Hide scrollbar for Chrome, Safari and Opera */
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }

            /* Hide scrollbar for IE, Edge and Firefox */
            .scrollbar-hide {
                -ms-overflow-style: none;
                /* IE and Edge */
                scrollbar-width: none;
                /* Firefox */
            }

            /* White SVGs for mobile */

            @media (max-width: 767px) {
                body #main-header-wrap .header_nav_link,
                body #main-header-wrap .hamburger line,
                body #main-header-wrap .search circle,
                body #main-header-wrap .search path,
                body #main-header-wrap .account path,
                body #main-header-wrap .cart path,
                body #main-header-wrap .equals_times:not(.active):before,
                body #main-header-wrap .equals_times:not(.active):after,
                body #main-header-wrap #cart_buble,
                body #main-header-wrap [js-flag-icon],
                body #main-header-wrap lottie-player svg path {
                    transition: color .3s linear, border-color .3s linear, background .3s linear, stroke .3s linear, fill .3s linear !important;
                    transition-delay: 0s !important;
                }

                body.header_sticky #main-header-wrap .header_nav_link,
                body.header_sticky #main-header-wrap .hamburger line,
                body.header_sticky #main-header-wrap .search circle,
                body.header_sticky #main-header-wrap .search path,
                body.header_sticky #main-header-wrap .account path,
                body.header_sticky #main-header-wrap .cart path,
                body.header_sticky #main-header-wrap .equals_times:not(.active):before,
                body.header_sticky #main-header-wrap .equals_times:not(.active):after,
                body.header_sticky #main-header-wrap #cart_buble,
                body.header_sticky #main-header-wrap [js-flag-icon],
                body.header_sticky #main-header-wrap lottie-player svg path {
                    transition-delay: .3s !important;
                }

                body:not(.header_sticky):not(.search_open) #main-header-wrap .header_nav_link {
                    color: #000 !important;
                }

                body:not(.header_sticky):not(.search_open) #main-header-wrap .hamburger line,
                body:not(.header_sticky):not(.search_open) #main-header-wrap .search circle,
                body:not(.header_sticky):not(.search_open) #main-header-wrap .search path,
                body:not(.header_sticky):not(.search_open) #main-header-wrap .account path,
                body:not(.header_sticky):not(.search_open) #main-header-wrap .cart path {
                    stroke: #000 !important;
                }

                body:not(.header_sticky):not(.search_open) #main-header-wrap .equals_times:not(.active):before,
                body:not(.header_sticky):not(.search_open) #main-header-wrap .equals_times:not(.active):after {
                    background: #000 !important;
                }

                body:not(.header_sticky):not(.search_open) #main-header-wrap #cart_buble {
                    background: #000 !important;
                    color: #0d0e0e !important;
                }

                body:not(.header_sticky):not(.search_open) #main-header-wrap [js-flag-icon] {
                    border-color: #000 !important;
                }

                body:not(.header_sticky):not(.search_open) #main-header-wrap lottie-player svg path {
                    fill: #000 !important;
                }
            }

            @media (min-width: 768px) {
                body #main-header-wrap:not(:hover) .header_nav_link,
                body #main-header-wrap:not(:hover) .hamburger line,
                body #main-header-wrap:not(:hover) .search circle,
                body #main-header-wrap:not(:hover) .search path,
                body #main-header-wrap:not(:hover) .account path,
                body #main-header-wrap:not(:hover) .cart path,
                body #main-header-wrap:not(:hover) .equals_times:not(.active):before,
                body #main-header-wrap:not(:hover) .equals_times:not(.active):after,
                body #main-header-wrap:not(:hover) #cart_buble,
                body #main-header-wrap:not(:hover) [js-flag-icon],
                body #main-header-wrap:not(:hover) lottie-player svg path {
                    transition: color .3s linear, border-color .3s linear, background .3s linear, stroke .3s linear, fill .3s linear, path .3s linear !important;
                    transition-delay: 0s !important;
                }

                body.header_sticky #main-header-wrap:not(:hover) .header_nav_link,
                body.header_sticky #main-header-wrap:not(:hover) .header_nav_link,
                body.header_sticky #main-header-wrap:not(:hover) .hamburger line,
                body.header_sticky #main-header-wrap:not(:hover) .search circle,
                body.header_sticky #main-header-wrap:not(:hover) .search path,
                body.header_sticky #main-header-wrap:not(:hover) .account path,
                body.header_sticky #main-header-wrap:not(:hover) .cart path,
                body.header_sticky #main-header-wrap:not(:hover) .equals_times:not(.active):before,
                body.header_sticky #main-header-wrap:not(:hover) .equals_times:not(.active):after,
                body.header_sticky #main-header-wrap:not(:hover) #cart_buble,
                body.header_sticky #main-header-wrap:not(:hover) [js-flag-icon],
                body.header_sticky #main-header-wrap:not(:hover) lottie-player svg path {
                    transition-delay: .3s !important;
                }

                body:not(.header_sticky):not(.search_open) #main-header-wrap:not(:hover) .header_nav_link {
                    color: #000 !important;
                }

                body:not(.header_sticky):not(.search_open) #main-header-wrap:not(:hover) .hamburger line,
                body:not(.header_sticky):not(.search_open) #main-header-wrap:not(:hover) .search circle,
                body:not(.header_sticky):not(.search_open) #main-header-wrap:not(:hover) .search path,
                body:not(.header_sticky):not(.search_open) #main-header-wrap:not(:hover) .account path,
                body:not(.header_sticky):not(.search_open) #main-header-wrap:not(:hover) .cart path {
                    stroke: #000 !important;
                }

                body:not(.header_sticky):not(.search_open) #main-header-wrap:not(:hover) .equals_times:not(.active):before,
                body:not(.header_sticky):not(.search_open) #main-header-wrap:not(:hover) .equals_times:not(.active):after {
                    background: #000 !important;
                }

                body:not(.header_sticky):not(.search_open) #main-header-wrap:not(:hover) #cart_buble {
                    background: #000 !important;
                    color: #0d0e0e !important;
                }

                body:not(.header_sticky):not(.search_open) #main-header-wrap:not(:hover) [js-flag-icon] {
                    border-color: #000 !important;
                }

                body:not(.header_sticky):not(.search_open) #main-header-wrap:not(:hover) lottie-player svg path {
                    fill: #000 !important;
                }
            }

        </style>


    </div>

    <div id="shopify-section-mobile-menu-main" class="shopify-section">


        <!-- MOBILE MENU CONTAINER -->
        <div :class="mobileMenu ? '!visible' : ''" class="invisible">
            <div
                    id="mainMobMenu"
                    style="transition: all 0.4s ease-in-out"
                    class="fixed z-10 top-0 h-[100vh] h-[100dvh] mr-[75px] w-[calc(300px)] overflow-hidden transition-[visibility] duration-700 ease-[ease]"
                    x-cloak
                    :class="mobileMenu !== false ? '' : '!ml-[-500px]'">
                <div
                        class="bg-white h-full left-0 relative z-[99999] slide_from_left transition-all duration-700 overflow-hidden"
                        :class="mobileMenu === true ? 'active' : ''"
                >
                    <div class="overflow-auto h-full | flex flex-col gap-8">
                        <div>


                            <!-- MOBILE MENU NAV -->
                            <div class="w-[34px] h-[34px] bg-white absolute top-[12.5px] left-[15px] z-[2]"></div>
                            <div class="relative z-[1]">
                                <div class="grid pt-[3.188rem] px-[24px]">
                                    <ul>

                                        @foreach($categories as $category)
                                            @if($category->subcategory()->count() == 0)
                                                <li class="border-b bottom-[#D9D9D9]">
                                                    <a href="{{route("category",['type'=>"category","id"=>$category->id])}}"
                                                       aria-label="Studio Sneaker" aria-handle="studio-sneaker"
                                                       class="py-[18px] text-[12px] leading-[15px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">
                                                        <span class="@if($current == $category->id) font-bold @endif">{{$category->name}}</span>
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                        @foreach($categories as $category)
                                            @if($category->subcategory()->count() != 0)
                                                <li x-data="{ openSub: false, hasNew: false }"
                                                    class="border-b bottom-[#D9D9D9]">
                                                    <button aria-label="SHOP CATEGORY" x-on:click="openSub = !openSub"
                                                            class="py-[18px] text-[12px] leading-[15px] uppercase text-black hover:text-gray-900 flex items-center justify-between w-full z-[999]">
                                            <span class="@if($current == $category->id) font-bold @endif">{{$category->name}}<template x-if="hasNew && !openSub"><sup
                                                            class="pl-[2px] font-[700] text-[6px] inline-block -translate-y-[3.5px]">NEW</sup></template></span>
                                                        <span :class="openSub && 'rotate-180'" class="x-transition">
  <svg
          width="7"
          height="13"
          viewBox="0 0 7 13"
          fill="none"
          xmlns="http://www.w3.org/2000/svg" role="presentation">
    <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M0 12.1211L6.06066 6.06043L-5.2984e-07 -0.000226974L-4.68022e-07 1.41399L4.64645 6.06043L-6.18172e-08 10.7069L0 12.1211Z"
            fill="#737373"/>
  </svg>

</span>
                                                    </button>
                                                    <div x-show="openSub" x-transition>
                                                        <ul class="pb-[18px]">

                                                            @foreach($category->subcategory()->orderBy("position")->get() as $subcategory)
                                                                <li>
                                                                    <a href="{{route("category",["type"=>"subcategory","id"=>$subcategory->id])}}"
                                                                       aria-label="All"
                                                                       class="px-[1.56rem] mb-[18px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">
                                                                        <span>{{$subcategory->name}}</span>
                                                                    </a>
                                                                </li>
                                                            @endforeach

                                                        </ul>
                                                    </div>

                                                </li>
                                            @endif
                                        @endforeach

                                    </ul>

                                </div>
                            </div>
                        </div>


                        <!--THE VAULT IS OPEN BANNER -->


                        <!--Localization -->
                        <div class="sticky z-[1] bg-white bottom-0 mt-auto px-[24px]">

                            <button
                                class="text-[12px] w-full text-left flex items-center border-t border-[#D9D9D9] pt-[19px] pb-[10px]"
                                aria-label="Change site location" onclick="countryPopup(); closeMobileMenu();">
                                <span>Shipping to: </span>
                                <div class="flex items-center">
                                    <span class="sr-only" js-flag-name>United Kingdom</span>
                                    <span js-flag-icon
                                          class="w-[13px] h-[13px] ml-[5px] rounded-full border-solid border-black border bg-center bg-cover"></span>

                                    <svg
                                        class="arrow-down"
                                        width="8"
                                        height="4"
                                        viewBox="0 0 8 4"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg" role="presentation">
                                        <path
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M0 0L3.7091 3.7091L7.4182 0H6.00398L3.7091 2.29488L1.41421 0H0Z"
                                            fill="white"/>
                                    </svg>


                                </div>
                            </button>

                        </div>
                    </div>
                </div>
                <!--End naw wrap -->

                <div
                    x-cloak
                    id="mainMobMenu__backdrop"
                    class="bg-black bg-opacity-20 h-[100vh] h-[100dvh] w-full z-10 fixed top-0 right-0 bottom-0 left-0 transition-[opacity] duration-700 ease-[ease]"
                    :class="mobileMenu !== false ? 'opacity-1' : 'opacity-0 hidden'"
                    style="-webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px);"
                    @click="mobileMenu = !mobileMenu; noScroll = !noScroll; if(document.querySelector('#launcher')) { document.querySelector('#launcher').style.display = 'block';  if(document.querySelector('.uwy')) { document.querySelector('.uwy').style.display = 'block'; }}"
                    x-swipe:left="mobileMenu = !mobileMenu; noScroll = !noScroll; if(document.querySelector('#launcher')) { document.querySelector('#launcher').style.display = 'block';  if(document.querySelector('.uwy')) { document.querySelector('.uwy').style.display = 'block'; }};"
                ></div>
            </div>
        </div>


    </div>
    <div id="shopify-section-mobile-nav-clothing" class="shopify-section">
        <div
            id="clothing"
            x-cloak
            class="submenu-all fixed top-0 left-0 right-0 h-[100vh] h-[100dvh] w-full overflow-hidden pb-[15px] max-w-[calc(100vw-75px)] mr-[75px] z-30"
            :class="mobileMenu === 'clothing' ? 'visible' : 'invisible'"
        >
            <div
                class="absolute top-0 w-full bg-white h-full translate-x-0 transition-all duration-700 ease-[ease] overflow-y-scroll"
                :class="[
      mobileMenu === false ? 'slide_from_left' : 'slide_from_right',
      mobileMenu === 'clothing' ? 'active' : ''
    ]"
            >
                <div class="flex justify-end p-[17px] bg-white sticky top-0 z-10">
                    <div x-on:click="mobileMenu = true; noScroll = true"
                         class="flex items-center text-primary-gray uppercase text-[0.75rem] cursor-pointer py-[8px]">
                        <svg
                            width="7"
                            height="13"
                            viewBox="0 0 7 13"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg" role="presentation">
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M6.06055 0L-0.000112938 6.06066L6.06055 12.1213L6.06055 10.7071L1.4141 6.06066L6.06055 1.41421L6.06055 0Z"
                                fill="#737373"/>
                        </svg>
                        <span class="ml-[0.5rem]">Back</span>
                    </div>
                </div>
                <div class="font-global_weight uppercase text-[1.25rem] px-[1.56rem]">Shop by Category</div>
                <!--clothing SLIDER -->
                <div class="pl-[1.56rem] overflow-hidden py-[15px] bg-white">
                    <div
                        x-data="swiper({
          spaceBetween: 1,
          slidesPerView: 2.25,
          centeredSlides: false,
          loop: false
        })"
                        class="swiper"
                    >
                        <div class="swiper-wrapper">

                            <div class="swiper-slide">

                            </div>

                            <div class="swiper-slide">

                            </div>

                            <div class="swiper-slide">

                            </div>

                        </div>
                    </div>
                </div>
                <!--clothing Menu -->


                <a href="https://representclo.com/collections/discover-all-products"
                   aria-label="/collections/discover-all-products"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">All</a>


                <a href="https://representclo.com/collections/streetwear-hoodies"
                   aria-label="/collections/streetwear-hoodies"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Hoodies</a>


                <a href="https://representclo.com/collections/t-shirts" aria-label="/collections/t-shirts"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">T-shirts</a>


                <a href="https://representclo.com/collections/jackets" aria-label="/collections/jackets"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Outerwear</a>


                <a href="https://representclo.com/collections/pants" aria-label="/collections/pants"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Pants</a>


                <a href="https://representclo.com/collections/footwear-all" aria-label="/collections/footwear-all"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Footwear</a>


                <a href="https://representclo.com/collections/sweaters" aria-label="/collections/sweaters"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Sweaters</a>


                <a href="https://representclo.com/collections/accessories" aria-label="/collections/accessories"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Accessories</a>


                <a href="https://representclo.com/collections/denim" aria-label="/collections/denim"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Denim</a>


                <a href="https://representclo.com/collections/shorts" aria-label="/collections/shorts"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Shorts</a>


                <a href="https://representclo.com/collections/knitwear" aria-label="/collections/knitwear"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Knitwear</a>


                <a href="https://representclo.com/collections/streetwear-sweatpants"
                   aria-label="/collections/streetwear-sweatpants"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Sweatpants</a>


                <a href="https://representclo.com/collections/mens-cargo-pants"
                   aria-label="/collections/mens-cargo-pants"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Cargo
                    Pants</a>


                <a href="https://representclo.com/collections/shirts" aria-label="/collections/shirts"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Shirts</a>


                <a href="https://representclo.com/collections/streetwear-hats" aria-label="/collections/streetwear-hats"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Caps</a>


                <a href="https://representclo.com/collections/puffer-jackets" aria-label="/collections/puffer-jackets"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Puffer
                    Jackets</a>


                <a href="https://representclo.com/collections/tank-tops" aria-label="/collections/tank-tops"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Vests</a>


                <a href="https://representclo.com/collections/streetwear-backpacks"
                   aria-label="/collections/streetwear-backpacks"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Bags</a>


                <a href="https://representclo.com/collections/streetwear-sunglasses"
                   aria-label="/collections/streetwear-sunglasses"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Sunglasses</a>


                <a href="https://representclo.com/collections/socks" aria-label="/collections/socks"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Socks</a>


                <a href="https://representclo.com/collections/jewellery" aria-label="/collections/jewellery"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Jewellery</a>


                <a href="https://representclo.com/collections/swimwear" aria-label="/collections/swimwear"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Swimwear</a>


            </div>
        </div>


    </div>
    <div id="shopify-section-mobile-nav-collection" class="shopify-section">
        <div
            id="collections"
            x-cloak
            class="submenu-all fixed top-0 left-0 right-0 h-[100vh] h-[100dvh] w-full overflow-hidden pb-[15px] max-w-[calc(100vw-75px)] mr-[75px] z-30"
            :class="mobileMenu === 'collections' ? 'visible' : 'invisible'"
        >
            <div
                class="absolute top-0 w-full bg-white h-full translate-x-0 transition-all duration-700 ease-[ease] overflow-y-scroll"
                :class="[
      mobileMenu === false ? 'slide_from_left' : 'slide_from_right',
      mobileMenu === 'collections' ? 'active' : ''
    ]"
            >
                <div class="flex justify-end p-[17px] bg-white sticky top-0 z-10">
                    <div x-on:click="mobileMenu = true; noScroll = true;"
                         class="flex items-center text-primary-gray uppercase text-[0.75rem] cursor-pointer py-[8px]">

                        <svg
                            width="7"
                            height="13"
                            viewBox="0 0 7 13"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg" role="presentation">
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M6.06055 0L-0.000112938 6.06066L6.06055 12.1213L6.06055 10.7071L1.4141 6.06066L6.06055 1.41421L6.06055 0Z"
                                fill="#737373"/>
                        </svg>


                        <span class="ml-[0.5rem]">Back</span>
                    </div>
                </div>
                <div class="font-global_weight uppercase text-[1.25rem] px-[1.56rem]">Shop by Collection</div>
                <!--collections SLIDER -->
                <div class="pl-[1.56rem] overflow-hidden py-[15px] bg-white">
                    <div
                        x-data="swiper({
          spaceBetween: 1,
          slidesPerView: 2.25,
          centeredSlides: false,
          loop: false
        })"
                        class="swiper"
                    >
                        <div class="swiper-wrapper">

                            <div class="swiper-slide">

                            </div>

                            <div class="swiper-slide">

                            </div>

                            <div class="swiper-slide">

                            </div>

                            <div class="swiper-slide">

                            </div>

                        </div>
                    </div>
                </div>
                <!--collections Menu -->


                <a href="https://representclo.com/collections/fall-winter" aria-label="/collections/fall-winter"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Fall
                    Winter 23</a>

                <a href="https://representclo.com/collections/mens-247" aria-label="/collections/mens-247"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">247</a>

                <a href="https://representclo.com/collections/owners-club" aria-label="/collections/owners-club"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Owners
                    Club</a>

                <a href="https://representclo.com/collections/initial" aria-label="/collections/initial"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Initial</a>

                <a href="https://representclo.com/collections/graphics-collection"
                   aria-label="/collections/graphics-collection"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Graphics</a>

                <a href="https://representclo.com/collections/247-x-puresport" aria-label="/collections/247-x-puresport"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">247
                    X PURESPORT</a>

                <a href="https://representclo.com/collections/mini-owners-club"
                   aria-label="/collections/mini-owners-club"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Mini
                    Owners Club</a>


                <a href="https://representclo.com/collections/tailored-collection"
                   aria-label="/collections/tailored-collection"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Tailored
                    Collection</a>

                <a href="https://representclo.com/collections/mens-blanks-all" aria-label="/collections/mens-blanks-all"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">BLANK</a>

                <a href="https://representclo.com/collections/summer" aria-label="/collections/summer"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Summer
                    23</a>

                <a href="https://representclo.com/collections/patron-of-the-club"
                   aria-label="/collections/patron-of-the-club"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Patron
                    Of The Club</a>

                <a href="https://representclo.com/collections/247-x-marchon" aria-label="/collections/247-x-marchon"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">247
                    X MARCHON</a>

                <a href="https://representclo.com/collections/represent-x-motley-crue"
                   aria-label="/collections/represent-x-motley-crue"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">REPRESENT
                    X Mötley Crüe</a>

                <a href="https://representclo.com/collections/new-era-caps" aria-label="/collections/new-era-caps"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">NEW
                    ERA CAPS</a>

                <a href="https://representclo.com/collections/represent-x-stock-x"
                   aria-label="/collections/represent-x-stock-x"
                   class="px-[1.56rem] py-[15px] text-[12px] leading-[12px] uppercase text-black hover:text-gray-700 flex items-center justify-between w-full">Represent
                    X Stock X</a>


            </div>
        </div>


    </div>
    <script>
        window.addEventListener("alpine:init", () => {
            Alpine.data("globalVars", () => ({
                MyHeader: {
                    open: false,
                    title: "test",
                    items: [],
                    id: 1,
                    show(name, data, id) {
                        if (data.length != 0) {
                            this.id = id;
                            this.open = true;
                            this.items = data;
                            this.title = name;
                        }
                    }
                }
            }))
        })
    </script>

</div>
