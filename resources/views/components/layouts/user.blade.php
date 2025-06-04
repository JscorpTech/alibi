@php use Illuminate\Support\Facades\Cookie; @endphp
    <!doctype html>
<html
    class="no-js overflow-x-hidden"
    lang="en"
    x-data="{ mobileMenu: false, noScroll: false }"
    :class="noScroll !== false ? 'overflow-hidden' : ''"
>

<head>
    <!-- HTML Meta Tags -->
    <title>ALIBIMENSTYLE CLO | UZ</title>
    <meta name="description"
          content="Alibimenstyle современный мужской магазин одежды, мировой стиль, твой гид по стилю.">

    <!-- Facebook Meta Tags -->
    <meta property="og:url" content="https://www.alibimenstyle.uz/show/326">
    <meta property="og:type" content="website">
    <meta property="og:title" content="ALIBIMENSTYLE CLO | UZ">
    <meta property="og:description"
          content="Alibimenstyle современный мужской магазин одежды, мировой стиль, твой гид по стилю.">
    <meta property="og:image" content="https://www.alibimenstyle.uz/assets/img/og-logo.png">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:domain" content="alibimenstyle.uz">
    <meta property="twitter:url" content="https://www.alibimenstyle.uz/show/326">
    <meta name="twitter:title" content="ALIBIMENSTYLE CLO | UZ">
    <meta name="twitter:description"
          content="Alibimenstyle современный мужской магазин одежды, мировой стиль, твой гид по стилю.">
    <meta name="twitter:image" content="https://www.alibimenstyle.uz/assets/img/og-logo.png">

    <!-- Meta Tags Generated via https://opengraph.dev -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset("assets/favicon_io/apple-touch-icon.png") }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset("assets/favicon_io/favicon-32x32.png") }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("assets/favicon_io/favicon-16x16.png") }}">
    <link rel="manifest" href="{{ asset("assets/favicon_io/site.webmanifest") }}">

    <meta charset="UTF-8">
    <meta name="description" content="ALIBIMENSTYLE CLO | UZ">
    <meta name="keywords" content="ALIBIMENSTYLE CLO | UZ">
    <meta name="author" content="Azamov Samandar">

    <x-user.com.head/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100&display=swap" rel="stylesheet">


</head>
<body class="font-displ ay index-"
      :class="{'mobile-nav-open': mobileMenu}"
      style="--top-bar-height: 0px; --top-bar-countdown-height: 0px; --header-height: 59px;"
      x-data="
      {
        renderDesktopMenuBackdrop: false,
        searchOpen: false,
        mobileSizeOpen: false,
        mobileSizeDenim1Open: false,
        mobileSizeDenim2Open: false,
        mobileSizeDenim3Open: false,
        mobileSizeDenim4Open: false,
        mobileSizeDenim5Open: false,
        mobileSizeDenim6Open: false,
        mobileSizeDenim7Open: false,
        mobileSizeDenim8Open: false,
        klarnaPopup: false,
        prestigePopup: false,
        bundlesPopup: false,
        wishlistPopup: false,
        is_navbar:false,
        qproduct: null,
        wishlist: [],
        init(){
          window.swymPageLoad = function(){
            this.updateWishlist()
          }

          window.addEventListener('quickviewOpened', event => {
            this.qproduct = event.detail.product
          })

          window.addEventListener('wishlistUpdated', event => {
            this.inWishlist(this.qproduct)
          })
        },
        addToCart(variant_id, isUpsell){
          let object = {};
          let jsondata = JSON.stringify(object);
          jsondata = JSON.parse(jsondata)
          jsondata.id = variant_id
            fetch(window.Shopify.routes.root + 'cart/add.js', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify(jsondata)
            })
          .then(response => {
            return response.json();
          })
          .then(response => {
            if(isUpsell){
              this.$dispatch('cart-updated',{ data: 'added', product: response.product_title })
            }else{
              this.$dispatch('cart-updated')
            }
            this.$dispatch('toggle-cart')
            this.$dispatch('add-bodyclass')
            this.$dispatch('cart-filter')
          })
          .catch((error) => {
            console.error('Error:', error);
          });
        },
        toggleSearch(){
          this.searchOpen = !this.searchOpen
        },
        addToWishlist(epi,empi,du,cu){
          const self = this;
          swat.addToWishList({
            epi: epi,
            empi: empi,
            du: du,
          }, function(response) {
              self.updateWishlist()
            }, function(error) {
              console.log('no', error)
            });
        },
        updateWishlist(){
          const local = this;
          swat.fetch(function(allWishlistedProducts) {
            const products = allWishlistedProducts?.map(item => item.empi);
            local.wishlist = products;
            console.log('wisthlist products', products);
            document.dispatchEvent(new CustomEvent('wishlistUpdated'));

            $dispatch('wishlistUpdated');
          }, function(error) {
            console.log('There was an error while fetching the wishlist', error)
          })
        },
        inWishlist(product){
          if(this.wishlist.length> 0 && this.wishlist.includes(product)){
            const wishlistIcon = document.querySelector('body .quickadd__wishlist');
            wishlistIcon.classList.add('wished');
          }
          return false
        },
        productInWishlist(product, $el){
          this.init()
          console.log(this.wishlist, SwymProductVariants)
          if(this.wishlist.length > 0 && this.wishlist.includes(product)){
            $el.classList.add('wished');
          }
          return false
        },
        isDiscounted(product) {
          if(parseInt(product.price) < parseInt(product.compare_at_price)) {
            return true;
          } else {
            return false;
          }
        },
      }
    ">

<div x-data="vars">
    <div x-data="globalVars">

        {{$top ?? ''}}
        <livewire:select-gender/>


        <div
            x-cloak
            :class="MyHeader.open ? '!visible' : ''"
            style="transition: all .4 ease-in-out;"
            class="fixed w-[100vw] invisible h-[100vh] z-[49] backdrop-filter backdrop-brightness-50 back">
            <div style="transition: all .4s ease-in-out" :class="MyHeader.open ? 'mt-[0px]' : ''" x-cloak
                 @mouseleave="MyHeader.open=false"
                 class="fixed z-[50] mt-[-500px] pt-[50px] h-[500px] w-[100%] bg-white">
                <div class="link-list flex flex-grow justify-between gap-14 px-9 py-[1.875rem]">
                    <div class="flex flex-col ">
                        <a :href="`/list/category/${MyHeader.id}`"
                           class="collection-navlink text-black text-[12px] uppercase hover:text-gray-700 whitespace-nowrap font-global_weight"
                           data-image="">
                            <span x-text="MyHeader.title"></span>
                        </a>

                        <template x-for="item in MyHeader.items">
                            <a :href="`/list/subcategory/${item.id}`"
                               class="collection-navlink text-black text-[12px] uppercase hover:text-gray-700 whitespace-nowrap megamenu_hover_sup pr-6">
                                <span class="text-black" x-text="item.name_ru"></span>
                                <span class="text-primary-gray" x-text="item.name_ru">Outerwear</span>
                            </a>
                        </template>

                    </div>

                </div>
            </div>
        </div>

        <x-user.com.header/>

        <a class="skip-to-content-link visually-hidden" href="index.html#MainContent">Skip to content</a>
        <div class="realVH fixed top-0 -z-10 pointer-events-none opacity-0 left-0 right-0 h-full w-full"></div>

        <div x-cloak x-ref="blur_onMobmenuOpen"
             class="relative | transition-[opacity] duration-700 ease-[ease] opacity-0">
            <div id="shopify-section-search-sitewide" class="shopify-section">
                <div class="searchTop pb-1 mt-6"
                     :class="searchOpen ? 'open' : ''"
                     x-data="{
                 products:[]
             }"
                     x-init="fetch(`{{env("APP_URL")}}/api/products`).then(res=>res.json()).then(function(res){
                                    products=res.data
                                    console.log(res.data)
                                })"
                >
            <span
                class="search_heading text-base uppercase font-global_weight mb-4 mx-4 hidden">Search results page</span>
                    <div class="flex flex-row items-center searchbar mx-4">
                        <div class="ml-3 mr-2">
                            <svg
                                width="13"
                                height="14"
                                viewBox="0 0 13 14"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                role="presentation"
                            >
                                <circle
                                    cx="5"
                                    cy="5"
                                    r="4.5"
                                    stroke="black"/>
                                <path d="M12 13L7.5 8.5" stroke="black"/>
                            </svg>
                        </div>
                        <div id="searchbox" class="w-full flex flex-row justify-between items-center pr-3 min-h-[32px]">
                            <form
                                action="https://representclo.com/search"
                                class="w-full flex items-center"
                                x-data="{
                            searchValue:'',
                        }"

                            >
                                <input type="text"

                                       class="w-full bg-transparent outline-none shadow-none border-none text-xs focus:shadow-none focus:border-0 focus:shadow-transparent placeholder:text-xs focus:ring-0"
                                       style="--tw-ring-shadow: none;"
                                       placeholder="Search for..."
                                       name="q"
                                       x-model="searchValue"
                                       x-on:input="fetch(`{{env("APP_URL")}}/api/products?search=${searchValue}`).then(res=>res.json()).then(function(res){
                                    products=res.data
                                    console.log(res.data)
                                })"
                                       x-on:focus="tagalysGetPopularSearches();if(tagalys_search == ''){noResultRecommendation();}"
                                >

                                <div class="text-xs text-[#737373] border-b mr-2 cursor-pointer"
                                     @click.prevent="searchValue = '';fetch(`{{env("APP_URL")}}/api/products`).then(res=>res.json()).then(function(res){
                                        products=res.data
                                        console.log(res.data)
                                    })">clear
                                </div>
                                <input type="hidden" name="type" value="product"/>
                                <input type="submit" class="sr-only" value="Search">
                            </form>
                            <button @click.prevent="searchOpen = false">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <line y1="-0.5" x2="18.3478" y2="-0.5"
                                          transform="matrix(0.693701 0.720263 -0.693701 0.720263 1 0.982178)"
                                          stroke="black"/>
                                    <line y1="-0.5" x2="18.3478" y2="-0.5"
                                          transform="matrix(0.693701 -0.720263 0.693701 0.720263 1.37012 14.3656)"
                                          stroke="black"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-row justify-between items-center mb-0 px-6 flex-wrap">
                        <div class="max-w-[calc(100%-85px)]">
                            <p x-show="products && textSuggestionload && tagalys_search != ''" x-cloak
                               class="text-[11px] mt-[22px] mb-0"><a :href="'/search?q=' + tagalys_search">View all
                                    results
                                    for
                                    “<span x-text="tagalys_search"></span>”</a></p>
                        </div>
                    </div>
                    <div data-search-hint=""></div>
                    <div class="overflow-hidden">
                        <div class="flex flex-row justify-between items-center mb-5 px-5"
                             data-search-topfilter="">
                            <div class="js-items-count searchCount" data-search-count=""></div>
                        </div>
                        <div class="right-panel relative">
                            <div class="search_grid mt-[22px]">
                                <div class="px-[30px] mb-[22px] w-full max-w-[350px]">
                                    <div x-show="!textSuggestionload">
                                        <div class="md:text-base text-[11px] mb-[12px] font-global_weight top-s"
                                             style="color:#848484;">
                                            Top Searches
                                        </div>
                                        <ul>
                                            <template x-for="ps in popularSearches">
                                                <li class="leading-[normal]">
                                                    <a :href="'/search?' + ps.queryString" x-text="ps.displayString"
                                                       class="text-[11px] leading-[normal]"></a>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                    <div x-show="textSuggestionload" x-cloak>
                                        <div class="md:text-base text-[11px] mb-[12px] font-global_weight top-s"
                                             style="color:#848484;">
                                            Suggestions
                                        </div>
                                        <ul>
                                            <template x-for="ts in textSuggest">
                                                <li class="leading-[normal]">
                                                    <a :href="'search?q=' + ts.rawQuery.query" x-text="ts.displayString"
                                                       class="text-[11px] leading-[normal]"></a>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                                <div class="w-full lg:px-[16px]">
                                    <div>
                                        <ul class="grid grid-cols-2 lg:grid-cols-4 2xl:grid-cols-5 gap-x-px gap-y-[35px] w-full"
                                            data-searchresult-products>

                                            <template x-for="product in products">
                                                <div>
                                                    <li>
                                                        <a :href="'/show/'+product.id">
                                                            <div class="relative overflow-hidden"
                                                                 @mouseleave="isQuickAddVisible = false;"
                                                                 @touchstart.outside="isQuickAddVisible = false;"
                                                            >
                                                                <div
                                                                    class="card-discount-label absolute text-[12px] leading-4 bottom-[10px] left-[15px] z-10"
                                                                    x-text="product.category.name"></div>
                                                                <span
                                                                    class="absolute top-4 right-4 z-10 | uppercase text-black text-xs | select-none"
                                                                    x-text="product.gender"></span>
                                                                <div
                                                                    class="flex w-full coll-image bg-[#f7f7f7] relative overflow-hidden aspect-[187/251] lg:aspect-[3/4]">
                                                                    <picture
                                                                        class="block object-cover h-full w-full overflow-hidden">
                                                                        <img
                                                                            :src="product.image"
                                                                            :alt="product.name"
                                                                            width="383"
                                                                            height="510"
                                                                            loading="lazy"
                                                                            class="block object-cover h-full w-full aspect-[187/251] lg:aspect-[3/4] scale-[1]"
                                                                        />
                                                                    </picture>
                                                                </div>
                                                                <div class="absolute bottom-0 right-0 z-[10]">
                                                                    <button
                                                                        x-show="!isQuickAddVisible"
                                                                        x-cloak
                                                                        tabindex="-1"
                                                                        type="button"
                                                                        class="p-[1rem] w-[45px] h-[45px]"
                                                                        @mouseover="isQuickAddVisible = true;"
                                                                        @touchstart.prevent="isQuickAddVisible = true;"
                                                                    >
                                                                        <svg
                                                                            width="13"
                                                                            height="16"
                                                                            viewBox="0 0 13 16"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            role="presentation"
                                                                        >
                                                                            <path
                                                                                d="M12.5 15.5H0.5V5.00025L11 5.00025L12.3001 5.00013L12.5 5.00009V15.5Z"
                                                                                stroke="black"
                                                                                stroke-linejoin="round"></path>
                                                                            <path
                                                                                d="M3.50004 5C3.50004 5 2.99999 1 6.5 1C10 1 9.49999 5 9.49999 5"
                                                                                stroke="black"></path>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                                <div :data-variiants-of="product.id"
                                                                     class="card-variants h-full  absolute w-full bottom-0 left-0 z-10 transition-[opacity] duration-300 ease-[ease] flex flex-col justify-end"
                                                                     :class="isQuickAddVisible ? 'variants-visible' : 'invisible pointer-events-none'"
                                                                >
                                                                    <div
                                                                        class="flex flex-row-reverse justify-center items-center p-[0.75rem] bg-zinc-100/[.9]">
                                                                        <div
                                                                            class="touch_only p-[1rem] -mr-[0.75rem]"
                                                                            @touchstart.prevent="isQuickAddVisible = false;"
                                                                        >
                                                                            <svg
                                                                                width="12"
                                                                                height="12"
                                                                                viewBox="0 0 18 17"
                                                                                fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                                role="presentation"
                                                                            >
                                                                                <line y1="-0.5" x2="22.6267" y2="-0.5"
                                                                                      transform="matrix(0.70713 -0.707084 0.70713 0.707084 1 17)"
                                                                                      stroke="black"/>
                                                                                <line y1="-0.5" x2="22.6267" y2="-0.5"
                                                                                      transform="matrix(0.70713 0.707084 -0.70713 0.707084 1 1)"
                                                                                      stroke="black"/>
                                                                            </svg>
                                                                        </div>
                                                                        <div class="scroll_hor p-px">
                                                                            <template
                                                                                x-for="variant in product.variants">
                                                                                <template x-if="variant.available">
                                                                                    <button type="button"
                                                                                            @click.prevent="addToCart(variant.id); isQuickAddVisible = false;"
                                                                                            :data-variant-id="variant.id"
                                                                                            class="shrink-0 w-[40px] h-[40px] select-none flex justify-center items-center outline outline-1 outline-[#CACACA] py-[0.5rem] text-[10px] hover:outline-black hover:z-10"
                                                                                            x-text='getVariantTitle(variant.title)'></button>
                                                                                </template>
                                                                            </template>
                                                                            <template
                                                                                x-for="variant in product.variants">
                                                                                <template x-if="!variant.available">
                                                                                    <button type="button" class="shrink-0 w-[40px] h-[40px] select-none flex justify-center items-center outline outline-1 outline-[#CACACA] py-[0.5rem] text-[10px]
                        text-gray-400
                        relative
                        overflow-hidden
                        after:content['']
                        after:block after:w-px
                        after:bg-[#CACACA]
                        after:h-[100px]
                        after:absolute
                        after:left-0
                        after:m-auto
                        after:rotate-45
                        after:right-0
                        after:top-0
                        after:bottom-0
                        after:z-[1]"
                                                                                            x-text='getVariantTitle(variant.title)'></button>
                                                                                </template>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <h3 class="mt-4 text-center font-global_weight uppercase f-adjust-on-gridchange text-xs "
                                                                :data-title="product.title.split(' - ')[0]"
                                                                x-text="product.name"></h3>
                                                            <span
                                                                class="lg:mb-4 flex flex-col lg:justify-center mt-1 gap-1 text-center w-full f-adjust-on-gridchange text-xs ">
    <span class="text-primary-gray">
        <span class="capitalize" x-text="product.options_with_values[1].values[0].toLowerCase()"></span>
    </span>
    <div class="flex flex-row justify-center">
        <template x-if="product.available">
            <div>
                <span x-show="isDiscounted(product)">
                    <span
                        class="uppercase text-[0.75rem] font-normal text-black line-through mr-[10px] text-primary-gray">$<span
                            x-text="product.compare_at_price"></span></span>
                </span>
                <span class="uppercase text-[0.75rem] font-normal text-black">$<span
                        x-text="product.price"></span></span>
            <div>
        </template>
        <div class="flex justify-between w-full p-4">
            <div></div>
            &nbsp;
            <span class="uppercase !font-normal text-[0.75rem] text-black"
                  x-text="product.price + ' UZS'"></span>
        </div>
    </div>
</span>
                                                        </a>
                                                    </li>
                                                </div>
                                            </template>
                                        </ul>
                                        <div x-show="products && textSuggestionload && tagalys_search != ''" x-cloak
                                             class="text-center mt-4">
                                            <a :href="'/search?q=' + tagalys_search"
                                               class="w-full max-w-[154px] btn_epic btn_epic__reversed block bg-white text-black border-solid border-black border uppercase text-[12px] tracking-widest px-6 py-2 my-auto cursor-pointer select-none mb-[20px]"
                                               id="collection_load_more" style="--btn_epic_height: 36px;">
                                                <div class="pointer-events-none"><span
                                                        aria-hidden="true">View All Results</span><span>View All Results</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div>
                {{$slot}}
                <x-user.com.footer/>
            </div>
        </div>

        <div id="shopify-section-loading-screen" class="shopify-section">
            <style type="text/css">
                #shopify-section-loading-screen {
                    display: none !important;
                }

                #header_wrapper,
                div[x-ref="blur_onMobmenuOpen"] {
                    display: block !important;
                    opacity: 1 !important;
                }
            </style>


        </div>


        <input type="hidden" id="locked_collection_enabled" value="false">
        <input type="hidden" id="locked_product_tags" value="">
        <input type="hidden" id="locked_collection_pw" value="%RZH&Sqjvd@*YBk">
        <input type="hidden" id="locked_collection_inputed_pw" value="">


        {{$ext_footer ?? ''}}


    </div>
</div>

@livewireScripts

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-SB9T7PMFZ6"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'G-SB9T7PMFZ6');
</script>


<script>
    const firebaseConfig = {
        apiKey: "AIzaSyCe6h8QJgJFRv-n3G_KcZJcqMTpsoyZ_AU",
        authDomain: "alibi-store.firebaseapp.com",
        projectId: "alibi-store",
        storageBucket: "alibi-store.appspot.com",
        messagingSenderId: "582982193026",
        appId: "1:582982193026:web:829f766dad7e614147899b",
        measurementId: "G-B9G4K899S8"
    }

</script>

<script src="https://www.gstatic.com/firebasejs/8.2.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.2.0/firebase-messaging.js"></script>

<script>
    // Firebase loyihasi konfiguratsiyasini boshlash
    firebase.initializeApp(firebaseConfig);

    // Firebase Messaging obyektini olish
    const messaging = firebase.messaging();

</script>
<script>
    Notification.requestPermission().then((permission) => {
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            messaging.getToken({vapidKey: "BKjIpwTJP04EB9Smp9s4dMqFo1BCij5UN_8k3gJAKlN1AURuSW0LaydVmpmhv8WhrQb-Itgk5Grol8t06_vUV8U"}).then((currentToken) => {
                if (currentToken) {
                    @if(!Cookie::has("is_fcm"))
                    fetch(`https://www.alibimenstyle.uz/api/update-fcm/?fcm_token=${currentToken}`)
                    @endif()
                    console.log('Firebase Token:', currentToken);
                } else {
                    console.log('No registration token available. Request permission to generate one.');
                }
            }).catch((err) => {
                console.log('An error occurred while retrieving token. ', err);
            });
            messaging.onMessage()
        } else {
            console.log('Unable to get permission to notify.');
        }
    });

</script>

</body>
</html>
