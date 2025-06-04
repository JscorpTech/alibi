@php use App\Models\Settings; @endphp
<div x-data="accordion">
    <div class="product-accordion-wrapper w-full pb-[33px] [@media(max-width:1023px)]:pb-0">
        <div class="accordion-header">
            <ul class="flex items-center">
                <li class="w-1/3">
                    <a x-on:click="accordion.tab=!accordion.tab"
                       class="cursor-pointer accordion-toggle font-global_weight font-body text-xs active"><span
                            class="dd-indicator"></span><span>{{__("product:detail")}}</span></a></li>
                <li class="w-1/3">
                    <a x-on:click="accordion.info=!accordion.info"
                       class="cursor-pointer accordion-toggle font-global_weight font-body text-xs active"><span
                            class="dd-indicator"></span><span>{{__("product:dostavca")}}</span></a></li>
            </ul>
        </div>
        <div class="accordion-content-group">
            <div class="accordion-content" :class="accordion.tab ? 'active' : ''">
                <div class="mob-accordion-toggle">
                    <h5 x-on:click="accordion.tab = !accordion.tab"
                        class="cursor-pointer font-global_weight font-body flex flex-row items-center gap-2 text-xs"
                        data-uw-rm-heading="level" role="heading" aria-level="3">{{__("product:detail")}}</h5>
                </div>
                <div class="content-container bg-white">
                    <div class="content-inner my-auto">
                        <div class="accordion-breadcrumbs">
						<span x-on:click="accordion.tab=!accordion.tab" class="close-accordion">
							<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg" role="presentation">
	<line y1="-0.5" x2="17" y2="-0.5" transform="matrix(0.70713 -0.707084 0.70713 0.707084 1 13.0205)"
          stroke="black"></line>
	<line y1="-0.5" x2="17" y2="-0.5" transform="matrix(0.70713 0.707084 -0.70713 0.707084 1 1)" stroke="black"></line>
</svg>
						</span>
                            <style>
                                .breadcrumbs {

                                    color: black;
                                    text-transform: capitalize;
                                }

                                .breadcrumbs__list {
                                    list-style-type: none;
                                    margin: 0;
                                    padding: 0;
                                }

                                .breadcrumbs__item {
                                    display: inline-block;
                                    margin: 0;
                                    padding: 0;
                                }

                                .breadcrumbs__item:not(:last-child):after {
                                    border-style: solid;
                                    border-width: .10em .10em 0 0;
                                    content: '';
                                    display: inline-block;
                                    height: .20em;
                                    margin: 0 .20em;
                                    position: relative;
                                    transform: rotate(45deg);
                                    vertical-align: middle;
                                    width: .20em;
                                }

                                .breadcrumbs__link {
                                    font-weight: 300;
                                    text-decoration: underline;
                                }

                                .breadcrumbs__link[aria-current="page"] {
                                    color: inherit;

                                    text-decoration: none;
                                }

                                .breadcrumbs__link[aria-current="page"]:hover,
                                .breadcrumbs__link[aria-current="page"]:focus {
                                    text-decoration: underline;
                                }

                                .breadcrumbs__item:not(:first-child):before {
                                    content: '>';
                                    margin-right: .60em;
                                }
                            </style>
                            <nav class="breadcrumbs text-xs " role="navigation" aria-label="breadcrumbs">
                                <ol class="breadcrumbs__list">
                                    <li class="breadcrumbs__item">
                                        <a class="breadcrumbs__link" href="/">{{__("home")}}</a>
                                    </li>

                                    <li class="breadcrumbs__item">
                                        <a class="breadcrumbs__link"
                                           href="#"
                                           x-on:click="accordion.tab=!accordion.tab"
                                           aria-current="page">{{$product->name}}</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="scrollable-container">

                            <div class="pb-4 pt-0">
                                <div class="prose | rte | text-default text-black">
                                    <span style="margin-left: 2em"></span>{!! $product->desc !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-content" :class="accordion.info ? 'active' : ''">
                <div class="mob-accordion-toggle">
                    <h5 x-on:click="accordion.info = !accordion.info"
                        class="cursor-pointer font-global_weight font-body flex flex-row items-center gap-2 text-xs"
                        data-uw-rm-heading="level" role="heading" aria-level="3">{{__("product:dostavca")}}</h5>
                </div>
                <div class="content-container bg-white">
                    <div class="content-inner my-auto">
                        <div class="accordion-breadcrumbs">
						<span x-on:click="accordion.info=!accordion.info" class="close-accordion">
							<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg" role="presentation">
	<line y1="-0.5" x2="17" y2="-0.5" transform="matrix(0.70713 -0.707084 0.70713 0.707084 1 13.0205)"
          stroke="black"></line>
	<line y1="-0.5" x2="17" y2="-0.5" transform="matrix(0.70713 0.707084 -0.70713 0.707084 1 1)" stroke="black"></line>
</svg>
						</span>
                            <style>
                                .breadcrumbs {

                                    color: black;
                                    text-transform: capitalize;
                                }

                                .breadcrumbs__list {
                                    list-style-type: none;
                                    margin: 0;
                                    padding: 0;
                                }

                                .breadcrumbs__item {
                                    display: inline-block;
                                    margin: 0;
                                    padding: 0;
                                }

                                .breadcrumbs__item:not(:last-child):after {
                                    border-style: solid;
                                    border-width: .10em .10em 0 0;
                                    content: '';
                                    display: inline-block;
                                    height: .20em;
                                    margin: 0 .20em;
                                    position: relative;
                                    transform: rotate(45deg);
                                    vertical-align: middle;
                                    width: .20em;
                                }

                                .breadcrumbs__link {
                                    font-weight: 300;
                                    text-decoration: underline;
                                }

                                .breadcrumbs__link[aria-current="page"] {
                                    color: inherit;

                                    text-decoration: none;
                                }

                                .breadcrumbs__link[aria-current="page"]:hover,
                                .breadcrumbs__link[aria-current="page"]:focus {
                                    text-decoration: underline;
                                }

                                .breadcrumbs__item:not(:first-child):before {
                                    content: '>';
                                    margin-right: .60em;
                                }
                            </style>
                        </div>
                        <div class="scrollable-container">

                            <div class="pb-4 pt-0">
                                <div class="prose | rte | text-default text-black">
                                    <div class="flex flex-row items-center">
                        <span class="ml-[10px] text-[10px] text-[#000000] font-medium uppercase">
                    {!! strip_tags(Settings::get("o")) !!}
                    
                    <style>
                        .product-usp-wrapper .country-row[js-flag-icon] {
                            background: none;
                            border-radius: 0;
                            border: 0;
                            height: auto;
                            width: auto;
                        }

                        .product-usp-wrapper .country-row:not(.fi-us, .fi-ca) {
                            display: none;
                        }</style>
                    <style>
                        @keyframes inventory-pulse {
                            0% {
                                opacity: 0.5;
                            }
                            to {
                                transform: scale(2.5);
                                opacity: 0;
                            }
                        }

                        .more-info.icon-inventory {
                            position: relative;
                            width: 10px;
                            height: 10px;
                            margin-left: 2px;
                            margin-right: 5px;
                        }

                        .icon-inventory:after,
                        .icon-inventory:before {
                            width: 9px;
                            height: 9px;
                            background: #34a853;
                            border-radius: 9px;
                            position: absolute;
                            left: 0;
                            top: 0;
                            content: '';
                        }

                        .icon-inventory.limited:after,
                        .icon-inventory.limited:before {
                            background: #f67637;
                        }

                        .icon-inventory:before {
                            animation: inventory-pulse 2s linear infinite;
                        }

                        .product-stock {
                            display: flex;
                            width: 100%;
                            flex-direction: column;
                            align-items: start;
                            justify-content: start;
                            padding: 0 14px 5px;
                        }

                        @media (min-width: 1024px) {
                            .product-stock {
                                align-items: center;
                                flex-direction: row;
                                padding: 0 0 5px;
                            }
                        }

                        .message-stock {
                            display: none;
                        }

                        .product-usp-wrapper .more-info.icon-inventory {
                            width: 8px;
                            margin-left: 0;
                            margin-right: 0;
                        }

                        .product-usp-wrapper .icon-inventory:after,
                        .product-usp-wrapper .icon-inventory:before {
                            width: 8px;
                            height: 8px;
                        }
                    </style>


                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div :class="accordion.sizeInfo ? 'active' : ''" class="accordion-content" data-accordion="4">
                <div class="content-container bg-white">
                    <div class="content-inner my-auto">
                        <div class="block absolute right-0 top-0 btn-accordion-close">
					<span class="close-accordion p-4 block">
						<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"
                             role="presentation">
	<line y1="-0.5" x2="17" y2="-0.5" transform="matrix(0.70713 -0.707084 0.70713 0.707084 1 13.0205)"
          stroke="black"></line>
	<line y1="-0.5" x2="17" y2="-0.5" transform="matrix(0.70713 0.707084 -0.70713 0.707084 1 1)" stroke="black"></line>
</svg>
					</span>
                        </div>
                        <div class="scrollable-container">


                            <div class="size-chart-modal mt-5">
                                <div class="col-lg-6">
                                    <div class="size-image">
                                        <p></p>
                                        <p>
                                            <img
                                                src="https://cdn.shopify.com/s/files/1/0181/2235/files/VINTAGE_247_TEE_CAD_page-0001.jpg?v=1689755610">
                                        </p>

                                        <div class="size_select">Select Size</div>
                                        <ul class="tabs">
                                            <li class="tab-active"><a href="#tab1">XS</a></li>
                                            <li><a href="#tab2">S</a></li>
                                            <li><a href="#tab3">M</a></li>
                                            <li><a href="#tab4">L</a></li>
                                            <li><a href="#tab5">XL</a></li>
                                            <li><a href="#tab6">XXL</a></li>
                                        </ul>
                                        <div id="tab1" class="tab-content col-count-4">
                                            <div class="measurement-title">Chest Width</div>
                                            <span class="measurement cm">56.25</span>
                                            <div class="measurement-title">Sleeve Length</div>
                                            <span class="measurement cm">18.5</span>
                                            <div class="measurement-title">Front Length</div>
                                            <span class="measurement cm">70.5</span>
                                            <div class="measurement-title">Shoulder</div>
                                            <span class="measurement cm">18</span>
                                        </div>
                                        <div id="tab2" class="tab-content col-count-4">
                                            <div class="measurement-title">Chest Width</div>
                                            <span class="measurement cm">57.5</span>
                                            <div class="measurement-title">Sleeve Length</div>
                                            <span class="measurement cm">19</span>
                                            <div class="measurement-title">Front Length</div>
                                            <span class="measurement cm">71</span>
                                            <div class="measurement-title">Shoulder</div>
                                            <span class="measurement cm">18.5</span>
                                        </div>
                                        <div id="tab3" class="tab-content col-count-4">
                                            <div class="measurement-title">Chest Width</div>
                                            <span class="measurement cm">60</span>
                                            <div class="measurement-title">Sleeve Length</div>
                                            <span class="measurement cm">20</span>
                                            <div class="measurement-title">Front Length</div>
                                            <span class="measurement cm">72</span>
                                            <div class="measurement-title">Shoulder</div>
                                            <span class="measurement cm">19</span>
                                        </div>
                                        <div id="tab4" class="tab-content col-count-4">
                                            <div class="measurement-title">Chest Width</div>
                                            <span class="measurement cm">63</span>
                                            <div class="measurement-title">Sleeve Length</div>
                                            <span class="measurement cm">21.5</span>
                                            <div class="measurement-title">Front Length</div>
                                            <span class="measurement cm">73.5</span>
                                            <div class="measurement-title">Shoulder</div>
                                            <span class="measurement cm">20</span>
                                        </div>
                                        <div id="tab5" class="tab-content col-count-4">
                                            <div class="measurement-title">Chest Width</div>
                                            <span class="measurement cm">66</span>
                                            <div class="measurement-title">Sleeve Length</div>
                                            <span class="measurement cm">23</span>
                                            <div class="measurement-title">Front Length</div>
                                            <span class="measurement cm">75</span>
                                            <div class="measurement-title">Shoulder</div>
                                            <span class="measurement cm">21</span>
                                        </div>
                                        <div id="tab6" class="tab-content col-count-4">
                                            <div class="measurement-title">Chest Width</div>
                                            <span class="measurement cm">69</span>
                                            <div class="measurement-title">Sleeve Length</div>
                                            <span class="measurement cm">24.5</span>
                                            <div class="measurement-title">Front Length</div>
                                            <span class="measurement cm">76.5</span>
                                            <div class="measurement-title">Shoulder</div>
                                            <span class="measurement cm">22</span>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-6">


                                    <div class="accordion mx-1" id="size-accordion">


                                        <div id="sizeAccordChart" class="lg:px-7 px-0">
                                            <div class="size-detail sizechart-inject">
                                                <div class="flex justify-between items-start mb-5">
                                                    <div class="text-left">


                                                    </div>
                                                    <div class="switcher-wrapper">
                                                        <span class="text-[7px]">CM</span>
                                                        <label class="switcher_label">
                                                            <input type="checkbox" name="size_guide"
                                                                   class="updated_data-switcher swap-measure js-swap-measure"
                                                                   data-uw-rm-form="fx"
                                                                   aria-label="hidden-control-element"
                                                                   data-uw-hidden-control="hidden-control-element"
                                                                   data-measure="cm">
                                                            <span class="switcher_slider"></span>
                                                            <span
                                                                style="color: #ffffff!important;background: #000000!important;clip: rect(1px, 1px, 1px, 1px)!important;clip-path: inset(50%)!important;height: 1px!important;width: 1px!important;margin: -1px!important;overflow: hidden!important;padding: 0!important;position: absolute!important;"
                                                                class="" data-uw-reader-element="" data-uw-rm-ignore="">Checkbox field</span></label>
                                                        <span class="text-[7px]">IN</span>
                                                    </div>
                                                </div>
                                                <div class="text-left !normal-case my-5">

                                                </div>
                                                <div class="js-size-grid" style="display: none;">
                                                    <p></p>
                                                    <p>
                                                        <img
                                                            src="https://cdn.shopify.com/s/files/1/0181/2235/files/VINTAGE_247_TEE_CAD_page-0001.jpg?v=1689755610">
                                                    </p>

                                                    <div class="size_select">Select Size</div>
                                                    <ul class="tabs">
                                                        <li class="tab-active"><a href="#tab1">XS</a></li>
                                                        <li><a href="#tab2">S</a></li>
                                                        <li><a href="#tab3">M</a></li>
                                                        <li><a href="#tab4">L</a></li>
                                                        <li><a href="#tab5">XL</a></li>
                                                        <li><a href="#tab6">XXL</a></li>
                                                    </ul>
                                                    <div id="tab1" class="tab-content col-count-4">
                                                        <div class="measurement-title">Chest Width</div>
                                                        <span class="measurement cm">56.25</span>
                                                        <div class="measurement-title">Sleeve Length</div>
                                                        <span class="measurement cm">18.5</span>
                                                        <div class="measurement-title">Front Length</div>
                                                        <span class="measurement cm">70.5</span>
                                                        <div class="measurement-title">Shoulder</div>
                                                        <span class="measurement cm">18</span>
                                                    </div>
                                                    <div id="tab2" class="tab-content d-none col-count-4">
                                                        <div class="measurement-title">Chest Width</div>
                                                        <span class="measurement cm">57.5</span>
                                                        <div class="measurement-title">Sleeve Length</div>
                                                        <span class="measurement cm">19</span>
                                                        <div class="measurement-title">Front Length</div>
                                                        <span class="measurement cm">71</span>
                                                        <div class="measurement-title">Shoulder</div>
                                                        <span class="measurement cm">18.5</span>
                                                    </div>
                                                    <div id="tab3" class="tab-content d-none col-count-4">
                                                        <div class="measurement-title">Chest Width</div>
                                                        <span class="measurement cm">60</span>
                                                        <div class="measurement-title">Sleeve Length</div>
                                                        <span class="measurement cm">20</span>
                                                        <div class="measurement-title">Front Length</div>
                                                        <span class="measurement cm">72</span>
                                                        <div class="measurement-title">Shoulder</div>
                                                        <span class="measurement cm">19</span>
                                                    </div>
                                                    <div id="tab4" class="tab-content d-none col-count-4">
                                                        <div class="measurement-title">Chest Width</div>
                                                        <span class="measurement cm">63</span>
                                                        <div class="measurement-title">Sleeve Length</div>
                                                        <span class="measurement cm">21.5</span>
                                                        <div class="measurement-title">Front Length</div>
                                                        <span class="measurement cm">73.5</span>
                                                        <div class="measurement-title">Shoulder</div>
                                                        <span class="measurement cm">20</span>
                                                    </div>
                                                    <div id="tab5" class="tab-content d-none col-count-4">
                                                        <div class="measurement-title">Chest Width</div>
                                                        <span class="measurement cm">66</span>
                                                        <div class="measurement-title">Sleeve Length</div>
                                                        <span class="measurement cm">23</span>
                                                        <div class="measurement-title">Front Length</div>
                                                        <span class="measurement cm">75</span>
                                                        <div class="measurement-title">Shoulder</div>
                                                        <span class="measurement cm">21</span>
                                                    </div>
                                                    <div id="tab6" class="tab-content d-none col-count-4">
                                                        <div class="measurement-title">Chest Width</div>
                                                        <span class="measurement cm">69</span>
                                                        <div class="measurement-title">Sleeve Length</div>
                                                        <span class="measurement cm">24.5</span>
                                                        <div class="measurement-title">Front Length</div>
                                                        <span class="measurement cm">76.5</span>
                                                        <div class="measurement-title">Shoulder</div>
                                                        <span class="measurement cm">22</span>
                                                    </div>

                                                </div>
                                                <div class="table-container">
                                                    <table
                                                        class="table table-striped table-bordered table-hover table-responsive">
                                                        <thead>
                                                        <tr>
                                                            <th>Size</th>
                                                            <th>Chest Width</th>
                                                            <th>Sleeve Length</th>
                                                            <th>Front Length</th>
                                                            <th>Shoulder</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>XS</td>
                                                            <td>56.25</td>
                                                            <td>18.5</td>
                                                            <td>70.5</td>
                                                            <td>18</td>
                                                        </tr>
                                                        <tr>
                                                            <td>S</td>
                                                            <td>57.5</td>
                                                            <td>19</td>
                                                            <td>71</td>
                                                            <td>18.5</td>
                                                        </tr>
                                                        <tr>
                                                            <td>M</td>
                                                            <td>60</td>
                                                            <td>20</td>
                                                            <td>72</td>
                                                            <td>19</td>
                                                        </tr>
                                                        <tr>
                                                            <td>L</td>
                                                            <td>63</td>
                                                            <td>21.5</td>
                                                            <td>73.5</td>
                                                            <td>20</td>
                                                        </tr>
                                                        <tr>
                                                            <td>XL</td>
                                                            <td>66</td>
                                                            <td>23</td>
                                                            <td>75</td>
                                                            <td>21</td>
                                                        </tr>
                                                        <tr>
                                                            <td>XXL</td>
                                                            <td>69</td>
                                                            <td>24.5</td>
                                                            <td>76.5</td>
                                                            <td>22</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>


                                        <style>
                                            .body-measurements h5,
                                            .body-measurements .tb-denim {
                                                display: none;
                                            }
                                        </style>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener("alpine:init", () => {

            Alpine.data("accordion", () => ({
                accordion: {
                    tab: window.screen.width < 1024,
                    sizeInfo: false,
                    info: false
                }
            }));
        });
    </script>
</div>
