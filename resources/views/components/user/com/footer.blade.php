@php use App\Enums\PageCategoryEnum;use App\Models\Pages;use App\Models\Settings; @endphp
<section id="shopify-section-main-footer" class="shopify-section">
    <footer class="footer m-auto pt-2.5 md:pt-7">
        <div class="footer__content-top page-width">
            <div class="footer__blocks-wrapper flex flex-col sm:flex-row justify-between">
                <div>
                    <div role="presentation" class="p-7 pb-0">
                        <div class="footer-block__heading font-medium mb-2.5 text-sm"><h1 class="text-center">
                                ПОДПИШИТЕСЬ НА НАШУ НОВОСТНУЮ РАССЫЛКУ</h1></div>

                        <div id="footer_form_ometria_newsletter_success" class="text-default mt-7 hidden">Thank
                            you for signing up.
                        </div>
                        <style>
                            #footer_form_ometria_newsletter:not(.footer_form_ometria_newsletter_show) .grid input ~ * {
                                display: none !important;
                            }
                        </style>
                        <script>
                            document.querySelector("#footer_form_ometria_newsletter .grid input:first-child").addEventListener('focus', function (e) {
                                if (e.target.nodeName == 'INPUT') {
                                    document.querySelector('#footer_form_ometria_newsletter').classList.add('footer_form_ometria_newsletter_show');
                                }
                            });
                            var selector = "#footer_form_ometria_newsletter"
                            var form = document.querySelectorAll(selector)[0];
                            form.addEventListener('submit', function (event) {
                                event.preventDefault();
                                var formElements = event.target.elements;
                                var urlEncodedDataPairs = Object.keys(formElements)
                                    .map(function (key) {
                                        return encodeURIComponent(formElements[key].name) + '=' + encodeURIComponent(formElements[key].value)
                                    });
                                var formBody = []
                                formBody = urlEncodedDataPairs.join('&').replace(/%20/g, '+');
                                fetch('https://api.ometria.com/forms/signup/ajax', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                                    },
                                    body: formBody
                                }).then(function () {
                                    //do anything
                                    document.querySelector('#footer_form_ometria_newsletter').classList.add('hidden');
                                    document.querySelector('#footer_form_ometria_newsletter_success').classList.remove('hidden');
                                })
                            })
                        </script>
                    </div>
                    <div class="mt-12 mb-11 px-7 md:my-4">
                        <div class=" flex flex-wrap justify-between" style="">
                            <a href="{!! strip_tags(Settings::get("instagram")) !!}" target="_blank"
                               class="w-8 h-auto text-gray-600 hover:text-black transition duration-150 flex items-center justify-center"
                               aria-label="Instagram (opens in a new tab)">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                     xmlns="http://www.w3.org/2000/svg" role="presentation">
                                    <path
                                        d="M15.9968 9.44127C18.1333 9.44127 18.3841 9.45079 19.2286 9.48889C20.0095 9.52381 20.4317 9.65397 20.7143 9.76508C21.0889 9.91111 21.3556 10.0825 21.6349 10.3619C21.9143 10.6413 22.0889 10.9079 22.2317 11.2825C22.3397 11.5651 22.473 11.9873 22.5079 12.7683C22.546 13.6127 22.5556 13.8635 22.5556 16C22.5556 18.1365 22.546 18.3873 22.5079 19.2317C22.473 20.0127 22.3429 20.4349 22.2317 20.7175C22.0857 21.0921 21.9143 21.3587 21.6349 21.6381C21.3556 21.9175 21.0889 22.0921 20.7143 22.2349C20.4317 22.3429 20.0095 22.4762 19.2286 22.5111C18.3841 22.5492 18.1333 22.5587 15.9968 22.5587C13.8603 22.5587 13.6095 22.5492 12.7651 22.5111C11.9841 22.4762 11.5619 22.346 11.2794 22.2349C10.9048 22.0889 10.6381 21.9175 10.3587 21.6381C10.0794 21.3587 9.90476 21.0921 9.7619 20.7175C9.65397 20.4349 9.52064 20.0127 9.48571 19.2317C9.44762 18.3873 9.4381 18.1365 9.4381 16C9.4381 13.8635 9.44762 13.6127 9.48571 12.7683C9.52064 11.9873 9.65079 11.5651 9.7619 11.2825C9.90794 10.9079 10.0794 10.6413 10.3587 10.3619C10.6381 10.0825 10.9048 9.90794 11.2794 9.76508C11.5619 9.65714 11.9841 9.52381 12.7651 9.48889C13.6095 9.44762 13.8603 9.44127 15.9968 9.44127ZM15.9968 8C13.8254 8 13.5524 8.00952 12.6984 8.04762C11.8476 8.08571 11.2667 8.22222 10.7587 8.41905C10.2317 8.62222 9.7873 8.89841 9.34286 9.34286C8.89841 9.7873 8.6254 10.2349 8.41905 10.7587C8.22222 11.2667 8.08571 11.8476 8.04762 12.7016C8.00952 13.5524 8 13.8254 8 15.9968C8 18.1683 8.00952 18.4413 8.04762 19.2952C8.08571 20.146 8.22222 20.727 8.41905 21.2381C8.62222 21.7651 8.89841 22.2095 9.34286 22.654C9.7873 23.0984 10.2349 23.3714 10.7587 23.5778C11.2667 23.7746 11.8476 23.9111 12.7016 23.9492C13.5556 23.9873 13.8254 23.9968 16 23.9968C18.1746 23.9968 18.4444 23.9873 19.2984 23.9492C20.1492 23.9111 20.7302 23.7746 21.2413 23.5778C21.7683 23.3746 22.2127 23.0984 22.6571 22.654C23.1016 22.2095 23.3746 21.7619 23.581 21.2381C23.7778 20.7302 23.9143 20.1492 23.9524 19.2952C23.9905 18.4413 24 18.1714 24 15.9968C24 13.8222 23.9905 13.5524 23.9524 12.6984C23.9143 11.8476 23.7778 11.2667 23.581 10.7556C23.3778 10.2286 23.1016 9.78413 22.6571 9.33968C22.2127 8.89524 21.7651 8.62222 21.2413 8.41587C20.7333 8.21905 20.1524 8.08254 19.2984 8.04444C18.4413 8.00952 18.1683 8 15.9968 8Z"
                                        fill="black"></path>
                                    <path
                                        d="M15.9966 11.8867C13.7299 11.8867 11.8887 13.7248 11.8887 15.9947C11.8887 18.2645 13.7299 20.1026 15.9966 20.1026C18.2633 20.1026 20.1045 18.2613 20.1045 15.9947C20.1045 13.728 18.2633 11.8867 15.9966 11.8867ZM15.9966 18.6613C14.5236 18.6613 13.3299 17.4677 13.3299 15.9947C13.3299 14.5216 14.5236 13.328 15.9966 13.328C17.4696 13.328 18.6633 14.5216 18.6633 15.9947C18.6633 17.4677 17.4696 18.6613 15.9966 18.6613Z"
                                        fill="black"></path>
                                    <path
                                        d="M20.2693 12.687C20.7988 12.687 21.228 12.2578 21.228 11.7283C21.228 11.1988 20.7988 10.7695 20.2693 10.7695C19.7398 10.7695 19.3105 11.1988 19.3105 11.7283C19.3105 12.2578 19.7398 12.687 20.2693 12.687Z"
                                        fill="black"></path>
                                </svg>
                            </a>
                            <a href="{!! strip_tags(Settings::get("facebook")) !!}" target="_blank"
                               class="w-8 h-auto text-gray-600 hover:text-black transition duration-150 flex items-center justify-center"
                               aria-label="Facebook (opens in a new tab)">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                     xmlns="http://www.w3.org/2000/svg" role="presentation">
                                    <path
                                        d="M20 13.5161H17.3333V12.086C17.3333 11.5591 17.8095 11.4086 18.0952 11.4086H20V9H17.3333C14.381 9 13.7143 10.8065 13.7143 11.9355V13.5161H12V16H13.7143V23H17.3333V16H19.7143L20 13.5161Z"
                                        fill="black"></path>
                                </svg>
                            </a>
                            <a href="{!! strip_tags(Settings::get("twitter")) !!}" target="_blank"
                               class="w-8 h-auto text-gray-600 hover:text-black transition duration-150 flex items-center justify-center"
                               aria-label="Twitter (opens in a new tab)">
                                <svg width="13" height="15" viewBox="0 0 1200 1227" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z"
                                        fill="#000"></path>
                                </svg>
                            </a>
                            <a href="{!! strip_tags(Settings::get("pinterest")) !!}" target="_blank"
                               class="w-8 h-auto text-gray-600 hover:text-black transition duration-150 flex items-center justify-center"
                               aria-label="Pinterest (opens in a new tab)">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                     xmlns="http://www.w3.org/2000/svg" role="presentation">
                                    <path
                                        d="M16.0004 8.00085C17.0839 8.00085 18.1187 8.21265 19.1048 8.63624C20.0909 9.05983 20.9415 9.62918 21.6566 10.3443C22.3717 11.0594 22.941 11.91 23.3646 12.8961C23.7882 13.8822 24 14.917 24 16.0004C24 17.4449 23.6425 18.7817 22.9274 20.0109C22.2123 21.2401 21.2401 22.2123 20.0109 22.9274C18.7817 23.6425 17.4449 24 16.0004 24C15.209 24 14.4521 23.8958 13.7296 23.6874C14.1321 23.0344 14.4028 22.4579 14.5418 21.958L15.1046 19.7709C15.2435 20.0345 15.5005 20.2671 15.8757 20.4686C16.2509 20.6702 16.6466 20.771 17.0628 20.771C17.8963 20.771 18.6428 20.5313 19.3021 20.0519C19.9614 19.5725 20.4718 18.9163 20.8333 18.0834C21.1948 17.2504 21.3753 16.313 21.3748 15.2711C21.3748 14.0351 20.8991 12.9621 19.9477 12.0523C18.9963 11.1425 17.7845 10.6876 16.3121 10.6876C15.4092 10.6876 14.5828 10.8405 13.8329 11.1462C13.0831 11.4519 12.479 11.8513 12.0207 12.3444C11.5624 12.8374 11.2117 13.372 10.9686 13.9482C10.7255 14.5244 10.6039 15.1043 10.6039 15.6879C10.6039 16.4098 10.7394 17.0418 11.0104 17.5838C11.2814 18.1258 11.6876 18.5078 12.2291 18.7299C12.3264 18.7714 12.4099 18.7748 12.4793 18.7401C12.5488 18.7054 12.5975 18.6393 12.6254 18.542C12.7227 18.1947 12.7782 17.9795 12.7919 17.8963C12.8477 17.7438 12.813 17.598 12.6877 17.4591C12.3267 17.0566 12.1463 16.5356 12.1463 15.8962C12.1463 14.8407 12.5109 13.9346 13.2402 13.1779C13.9696 12.4212 14.9244 12.0429 16.1046 12.0429C17.1465 12.0429 17.959 12.3276 18.542 12.8969C19.125 13.4663 19.4168 14.2093 19.4173 15.1259C19.4173 15.89 19.3132 16.5948 19.1048 17.2405C18.8964 17.8861 18.5978 18.4033 18.2089 18.7922C17.8201 19.1811 17.3757 19.3755 16.8758 19.3755C16.459 19.3755 16.1189 19.2226 15.8552 18.9169C15.5916 18.6112 15.5082 18.2499 15.605 17.8331C15.6608 17.6384 15.7337 17.3885 15.8236 17.0833C15.9136 16.7782 15.9899 16.5177 16.0525 16.3019C16.1151 16.0861 16.1741 15.8535 16.2293 15.6042C16.2845 15.3548 16.3124 15.1464 16.313 14.979C16.313 14.6317 16.2228 14.3471 16.0423 14.125C15.8618 13.903 15.5979 13.7919 15.2506 13.7919C14.8202 13.7919 14.4589 13.9898 14.1669 14.3855C13.8748 14.7812 13.729 15.2776 13.7296 15.8749C13.7296 16.0969 13.747 16.3121 13.7817 16.5205C13.8164 16.7289 13.8546 16.8818 13.8961 16.9791L13.9585 17.1252C13.3891 19.5973 13.0418 21.0485 12.9166 21.4789C12.8056 22.0067 12.764 22.6247 12.7919 23.333C11.3611 22.6942 10.2048 21.7149 9.32287 20.3952C8.44096 19.0755 8 17.6102 8 15.9996C8 13.7911 8.78114 11.9057 10.3434 10.3434C11.9057 8.78114 13.7911 8 15.9996 8L16.0004 8.00085Z"
                                        fill="black"></path>
                                </svg>
                            </a>
                            <a href="{!! strip_tags(Settings::get("tiktok")) !!}" target="_blank"
                               class="w-8 h-auto text-gray-600 hover:text-black transition duration-150 flex items-center justify-center"
                               aria-label="tiktok (opens in a new tab)">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                     xmlns="http://www.w3.org/2000/svg" role="presentation">
                                    <path
                                        d="M24 14.6515C22.5255 14.6546 21.0872 14.2522 19.8882 13.5013V18.7387C19.8878 19.7087 19.5489 20.6555 18.917 21.4525C18.285 22.2495 17.39 22.8587 16.3517 23.1986C15.3133 23.5384 14.1812 23.5929 13.1065 23.3545C12.0319 23.1162 11.066 22.5965 10.3381 21.8649C9.61015 21.1333 9.15481 20.2246 9.03298 19.2605C8.91115 18.2963 9.12862 17.3226 9.65632 16.4695C10.184 15.6164 10.9968 14.9246 11.986 14.4866C12.9751 14.0486 14.0935 13.8853 15.1916 14.0185V16.6528C14.6891 16.5145 14.1496 16.5186 13.6499 16.6647C13.1503 16.8107 12.7162 17.0911 12.4096 17.4659C12.103 17.8407 11.9395 18.2907 11.9426 18.7516C11.9457 19.2126 12.1152 19.6608 12.4268 20.0324C12.7385 20.404 13.1763 20.68 13.6779 20.8209C14.1795 20.9618 14.7191 20.9604 15.2197 20.8169C15.7203 20.6734 16.1563 20.3952 16.4654 20.022C16.7745 19.6488 16.9409 19.1996 16.9409 18.7387V8.5H19.8882C19.8862 8.71778 19.907 8.93526 19.9505 9.1497V9.1497C20.0529 9.6284 20.2659 10.0838 20.5763 10.488C20.8868 10.8922 21.2882 11.2368 21.756 11.5006C22.4217 11.8857 23.202 12.0909 24 12.0908V14.6515Z"
                                        fill="black"></path>
                                </svg>
                            </a>
                            <a href="{!! strip_tags(Settings::get('youtube')) !!}" target="_blank"
                               class="w-8 h-auto text-gray-600 hover:text-black transition duration-150 flex items-center justify-center"
                               aria-label="Youtube (opens in a new tab)">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                     xmlns="http://www.w3.org/2000/svg" role="presentation">
                                    <path
                                        d="M8 20.6932C8 21.3644 8.21101 21.9352 8.63302 22.4056C9.05503 22.876 9.56877 23.1109 10.1742 23.1104H21.8258C22.4312 23.1104 22.945 22.8754 23.367 22.4056C23.789 21.9357 24 21.3649 24 20.6932V10.9573C24 10.2861 23.7869 9.70873 23.3607 9.22524C22.9346 8.74175 22.4229 8.5 21.8258 8.5H10.1742C9.5776 8.5 9.06594 8.74175 8.63925 9.22524C8.21257 9.70873 7.99948 10.2861 8 10.9573V20.6932ZM13.8195 18.9341V12.7164C13.8195 12.6732 13.8277 12.6364 13.8439 12.6062C13.8582 12.5795 13.8885 12.5611 13.9185 12.5571C13.9314 12.5554 13.9413 12.5572 13.9539 12.5598L13.9602 12.5611C13.9858 12.5664 14.0104 12.5755 14.0333 12.5883L19.4464 15.6125C19.5317 15.6602 19.6266 15.7209 19.6266 15.8187C19.6266 15.8996 19.5393 15.946 19.4686 15.9854L14.051 19.0122C14.0164 19.0315 13.987 19.0683 13.9474 19.0683C13.9131 19.0683 13.8876 19.0593 13.871 19.0413C13.8367 19.0053 13.8195 18.9696 13.8195 18.9341Z"
                                        fill="black"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="w-full sm:w-1/4">
                    <div>
                        <details aria-label="Company" class="py-1.5 px-7 relative">
                            <summary type="button" aria-label="Company"
                                     class="w-full py-1.5 flex justify-between uppercase cursor-pointer"
                                     @click="removeAttributes($event)" tabindex="0">
                                <span class="footer-block__heading font-medium text-[12px]">{{__("company")}}</span>
                                <span class="plus_minus"></span>
                            </summary>
                            <div role="region">
                                <ul>
                                    @foreach(Pages::query()->where(['category'=>PageCategoryEnum::COMPANY])->get() as $page)
                                        <li class="m-2">
                                            <a href="{{route("base:page",$page->path)}}"
                                               class="text-[12px] uppercase font-light text-dark-neutral hover:text-gray-700">{{$page->title}}</a>
                                        </li>
                                    @endforeach
                                </ul>

                            </div>
                        </details>
                    </div>

                </div>
                <div class="w-full sm:w-1/4">
                    <div>
                        <details aria-label="Customer Service" class="py-1.5 px-7 relative">
                            <summary type="button" aria-label="Customer Service"
                                     class="w-full py-1.5 flex justify-between uppercase cursor-pointer"
                                     @click="removeAttributes($event)" tabindex="0">
                                        <span
                                            class="footer-block__heading font-medium text-[12px]">{{__("customer:service")}}</span>
                                <span class="plus_minus"></span>
                            </summary>
                            <div id="content__Customer Service" role="region">
                                <ul>
                                    <li class="m-2">
                                        <a href="{!! strip_tags(Settings::get("telegram")) !!}"
                                           class="text-[12px] uppercase font-light text-dark-neutral hover:text-gray-700">{{__("support")}}</a>
                                    </li>
                                </ul>
                                <ul>
                                    <li class="m-2">
                                        <a href="#"
                                           class="text-[12px] uppercase font-light text-dark-neutral hover:text-gray-700">{{__("faqs")}}</a>
                                    </li>
                                </ul>

                            </div>
                        </details>
                    </div>
                    <div>
                        <details class="py-1.5 px-7 relative" aria-label="Terms &amp; Privacy">
                            <summary type="button" aria-label="terms &amp; privacy"
                                     class="w-full py-1.5 flex justify-between uppercase cursor-pointer"
                                     @click="removeAttributes($event)" tabindex="0">
                                        <span
                                            class="footer-block__heading font-medium text-[12px]">{{__("terms")}}</span>
                                <span class="plus_minus"></span>
                            </summary>
                            <div id="content__terms &amp; privacy" role="region">
                                <ul>
                                    @foreach(Pages::query()->where(['category'=>PageCategoryEnum::TERMS])->get() as $page)
                                        <li class="m-2">
                                            <a href="{{route("base:page",$page->path)}}"
                                               class="text-[12px] uppercase font-light text-dark-neutral hover:text-gray-700">{{$page->title}}</a>
                                        </li>
                                    @endforeach
                                </ul>

                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer__content-bottom px-7">
            <div class="page-width sm:flex justify-between mt-2 pb-2">
                <div class="sm:mx-0 footer__column footer__column--info flex items-center flex-wrap">

                    <div class="w-full flex justify-between">
                        <div class="footer__copyright sm:flex">
                            <small class="copyright__content flex items-center text-xs text-primary-gray">
                                <a href="/" title="">Alibi</a>
                                © 2023 Felix-its
                            </small>
                        </div>
                        <a href="https://felix-its.uz" aria-label="Shipping to" class="flex items-center sm:hidden">
                            <img class="h-[20px] mr-5 mb-5" src="{{asset("assets/img/felix.png")}}" alt="">
                        </a>
                    </div>
                </div>
                <div class="hidden sm:flex items-center">
                    <div>
                        <a href="https://felix-its.uz" aria-label="Shipping to" class="flex items-center">
                            <img class="h-[40px] mr-5 mb-5" src="{{asset("assets/img/felix.png")}}" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <style>
        #shopify-section-main-footer {
            position: relative;
            z-index: 3;
        }

        #shopify-section-main-footer .arrow-down path,
        #mainMobMenu .arrow-down path {
            fill: var(--primary-dark);
        }

        #shopify-section-main-footer .arrow-down {
            margin-left: 15px;
        }

        #shopify-section-main-footer {
            background-color: #f8f8f8;
        }
    </style>
    <script>
        function removeAttributes(event) {
            const detailsElements = document.querySelectorAll('details');
            const currentDetail = event.target.closest('details');
            detailsElements.forEach((element) => {
                if (element !== currentDetail) {
                    element.removeAttribute('open');
                }
            });
        }

        const flag_icon_mob = document.getElementById("flag-icon-mobile");
        const flag_icon_desktop = document.getElementById("flag-icon-desktop");
        let shopifyCountry_mob = Shopify.country.toLowerCase();
        let flag_code_mob = "fi-" + shopifyCountry_mob;
        let flag_code_desktop = "fi-" + shopifyCountry_mob;
        flag_icon_mob.classList.add(flag_code_mob);
        flag_icon_desktop.classList.add(flag_code_desktop);
    </script>
</section>
