@extends('layouts.app')

@section('content')
    <div class="main-container exchange mt--3">
        <div v-cloak id="market-container">
            <div class="container header-exchange mt-4">
                <div class="row">
                    <div class="col">
                        <div class="boxed boxed--border navbar-resum">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="head-splits name">
                                        <h4 class="title">CREA<span class="gray-color">/CBD</span></h4>
                                        <p>CREA</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="head-splits">
                                        <p class=" gray-color">{{ __('lang.MARKET.LAST_PRICE') }}</p>
                                        <p class="amount"><strong><span class="green-color">@{{ ticker.latest }}</span></strong></p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="head-splits">
                                        <p class=" gray-color">{{ __('lang.MARKET.CHANGE_24H') }}</p>
                                        <p class="amount">
                                            <strong>
                                                <span v-bind:class="{'red-color': ticker.percent_change < 0, 'green-colo': ticker.percent_change >= 0}">
                                                    @{{ ticker.percent_change }}%
                                                </span>
                                            </strong>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="head-splits">
                                        <p class=" gray-color">{{ __('lang.MARKET.HIGH_24H') }}</p>
                                        <p class="amount"><strong><span class="gray-color">@{{ ticker.highest_bid }}</span></strong></p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="head-splits">
                                        <p class=" gray-color">{{ __('lang.MARKET.LOW_24H') }}</p>
                                        <p class="amount"><strong><span class="gray-color">@{{ ticker.lowest_ask }}</span></strong></p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="head-splits">
                                        <p class=" gray-color">{{ __('lang.MARKET.VOLUME_24H') }}</p>
                                        <p class="amount"><strong><span class="gray-color">@{{ parseAsset(ticker.cbd_volume).toPlainString(null, false) }}</span> CBD</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container content-exchange">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-3 order-md-2 order-lg-1">
                        <div class="boxed boxed--border">
                            <div class="tabs-container" data-content-align="left">
                                <ul class="tabs w-100 ul-market-buy-sell mb-0">
                                    <li class="active">
                                        <div class="tab__title text-center">
                                            <img v-lazy="'{{ asset('img/exchange/all.svg') }}'" alt="">
                                        </div>
                                        <div class="tab__content">

                                            <table id="sell-orders" class="display table-buy-sell-market" >
                                                <thead>
                                                <tr>
                                                    <th class="color-title-table-primary">{{ __('lang.MARKET.PRICE') }}</th>
                                                    <th>CREA</th>
                                                    <th>CBD</th>
                                                    {{--{#<th>Total CBD</th>#}--}}
                                                </tr>

                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>

                                            <table class="display table-buy-sell-market mb-0">
                                                <thead>
                                                <tr style="display: none;">
                                                    <th>{{ __('lang.MARKET.PRICE') }}</th>
                                                    <th>CREA</th>
                                                    <th>CBD</th>
                                                   {{-- {#<th>Total CBD</th>#}--}}
                                                </tr>

                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td colspan="3" class="text-center">
                                                        <p class="color-buy font-weight-bold" style="font-size: 14px;">
                                                            <span buy-last-price >0.000000</span> <i class="icon-To-Top"></i>
                                                        </p>
                                                    </td>
                                                    <td style="display: none;"></td>
                                                    <td style="display: none;"></td>
                                                    {{--{#<td style="display: none;"></td>#}--}}
                                                </tr>
                                                </tbody>
                                            </table>

                                            <table id="buy-orders" class="display buy_left_result_all" >
                                                <thead>
                                                <tr style="display: none;">
                                                    <th>{{ __('lang.MARKET.PRICE') }}</th>
                                                    <th>CREA</th>
                                                    <th>CBD</th>
                                                    {{--{#<th>Total CBD</th>#}--}}
                                                </tr>

                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>

                                        </div>
                                    </li>
                                    <li>
                                        <div class="tab__title text-center">
                                            <img v-lazy="'{{ asset('img/exchange/buy.svg') }}'" alt="">
                                        </div>
                                        <div class="tab__content">
                                            <table id="buy-left" class="display table-buy-sell-market mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{ __('lang.MARKET.PRICE') }}</th>
                                                    <th>CREA</th>
                                                    <th>CBD</th>
                                                    {{--{#<th>Total CBD</th>#}--}}
                                                </tr>

                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td colspan="3" class="text-center">
                                                        <p class="color-buy font-weight-bold" style="font-size: 14px;">
                                                            <span buy-last-price>0.000000</span> <i class="icon-To-Top"></i>
                                                        </p>
                                                    </td>
                                                    <td style="display: none;"></td>
                                                    <td style="display: none;"></td>
                                                    {{--{#<td style="display: none;"></td>#}--}}
                                                </tr>
                                                </tbody>
                                            </table>

                                            <table id="buy-orders-all" class="display table-buy-sell-market">
                                                <thead>
                                                <tr style="display: none">
                                                    <th>{{ __('lang.MARKET.PRICE') }}</th>
                                                    <th>CREA</th>
                                                    <th>CBD</th>
                                                    {{--{#<th>Total CBD</th>#}--}}
                                                </tr>

                                                </thead>
                                                <tbody>


                                                </tbody>
                                            </table>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="tab__title text-center">
                                            <img v-lazy="'{{ asset('img/exchange/sell.svg') }}'" alt="">
                                        </div>
                                        <div class="tab__content">
                                            <table id="sell-all-orders" class="display table-buy-sell-market">
                                                <thead>
                                                <tr>
                                                    <th>{{ __('lang.MARKET.PRICE') }}</th>
                                                    <th>CREA</th>
                                                    <th>CBD</th>
                                                    {{--{#<th>Total CBD</th>#}--}}
                                                </tr>

                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                            <table id="sell-left" class="display table-buy-sell-market mb-0">
                                                <thead>
                                                <tr style="display: none">
                                                    <th>{{ __('lang.MARKET.PRICE') }}</th>
                                                    <th>CREA</th>
                                                    <th>CBD</th>
                                                    {{--{#<th>Total CBD</th>#}--}}
                                                </tr>

                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td colspan="3" class="text-center">
                                                        <p class="color-sell font-weight-bold" style="font-size: 14px;">
                                                            <span sell-last-price>0.000000</span> <i class="icon-To-Top"></i>
                                                        </p>
                                                    </td>
                                                    <td style="display: none;"></td>
                                                    <td style="display: none;"></td>
                                                    {{--{#<td style="display: none;"></td>#}--}}
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </li>
                                </ul>
                            </div><!--end of tabs container-->
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6 order-md-1 order-lg-2">
                        <div v-pre class="boxed boxed--border" style="height: 280px;">
                            <div id="market-chart" style="height: 100%">

                            </div>
                        </div>

                        <div class="boxed boxed--border form-buy-sell">

                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="d-flex" style="justify-content: space-between;">
                                        <p class="title"><strong>{{ __('lang.MARKET.BUY_CREA') }}</strong></p>
                                        <p v-if="session"><i class="fas fa-wallet"></i> @{{ account.user.cbd_balance }}</p>
                                    </div>


                                    <div class="form-group">
                                        <label for="" class="text-uppercase">{{ __('lang.MARKET.PRICE') }}</label>

                                        <div class="input-icon">
                                            <i class="">CREA/CBD</i>
                                            <input v-model="buyForm.price"
                                                   v-on:input="inputBuy($event, 'price')"
                                                   v-on:blur="onParseBuyForm"
                                                   type="text" name="input" placeholder="{{ __('lang.MARKET.PRICE') }} CREA/CBD" step="0.001" />
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label for="" class="text-uppercase">{{ __('lang.MARKET.AMOUNT') }}</label>
                                        <div class="input-icon">
                                            <i class="">CREA</i>
                                            <input v-model="buyForm.amount"
                                                   v-on:input="inputBuy($event, 'amount')"
                                                   v-on:blur="onParseBuyForm"
                                                   type="text" name="input" placeholder="{{ __('lang.MARKET.AMOUNT') }} CREA" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="text-uppercase">{{ __('lang.MARKET.TOTAL') }}</label>
                                        <div class="input-icon">
                                            <i class="">CBD</i>
                                            <input v-model="buyForm.total"
                                                   v-on:input="inputBuy($event, 'total')"
                                                   v-on:blur="onParseBuyForm"
                                                   type="text" name="input" placeholder="{{ __('lang.MARKET.TOTAL_CREA') }}" />
                                        </div>
                                    </div>
                                    <div class="form-group text-center">
                                        <a class="btn btn-primary w-100 btn-buy-exchange" v-on:click="makeOrder('buy')">
                                            <span class="btn__text">{{ __('lang.MARKET.BUY') }}</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">

                                    <div class="d-flex" style="justify-content: space-between;">
                                        <p class="title">
                                            <strong>{{ __('lang.MARKET.SELL_CREA') }}</strong>
                                        </p>
                                        <p v-if="session">
                                            <i class="fas fa-wallet"></i> @{{ account.user.balance }}
                                        </p>
                                    </div>


                                    <div class="form-group">
                                        <label for="" class="text-uppercase">{{ __('lang.MARKET.PRICE') }}</label>

                                        <div class="input-icon">
                                            <i class="">CREA/CBD</i>
                                            <input v-model="sellForm.price"
                                                   v-on:input="inputSell($event, 'price')"
                                                   v-on:blur="onParseSellForm"
                                                   type="text" name="input" placeholder="{{ __('lang.MARKET.PRICE') }} CREA/CBD" />
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label for="" class="text-uppercase">{{ __('lang.MARKET.AMOUNT') }}</label>
                                        <div class="input-icon">
                                            <i class="">CREA</i>
                                            <input v-model="sellForm.amount"
                                                   v-on:input="inputSell($event, 'amount')"
                                                   v-on:blur="onParseSellForm"
                                                   type="text" name="input" placeholder="{{ __('lang.MARKET.AMOUNT') }} CREA" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="text-uppercase">{{ __('lang.MARKET.TOTAL') }}</label>
                                        <div class="input-icon">
                                            <i class="">CBD</i>
                                            <input v-model="sellForm.total"
                                                   v-on:input="inputSell($event, 'total')"
                                                   v-on:blur="onParseSellForm"
                                                   type="text" name="input" placeholder="{{ __('lang.MARKET.TOTAL') }} CREA" />
                                        </div>
                                    </div>
                                    <div class="form-group text-center">
                                        <a class="btn btn-sell-exchange color--white w-100" v-on:click="makeOrder('sell')">
                                            <span class="btn__text">{{ __('lang.MARKET.SELL') }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--{# Market History #}--}}
                    <div class="col-sm-12 col-md-12 col-lg-3 order-md-3 order-lg-3">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="subtitle-module-exchange">{{ __('lang.MARKET.MARKET_HISTORY') }}</p>
                            </div>
                        </div>
                        <div class="boxed boxed--border">
                            <table id="market-history" class="display market-history text-right" style="width:100%">
                                <thead>
                                <tr>
                                    <th>{{ __('lang.MARKET.DATE') }}</th>
                                    <th>{{ __('lang.MARKET.PRICE') }}</th>
                                    <th>CREA</th>
                                    <th>CBD</th>
                                </tr>
                                </thead>
                                <tbody>

{{--{#                                <template v-for="t in recentTrades">
                                    <tr>
                                        <td>@{{ t.date }}</td>
                                        <td v-bind:class="{'red-color': t.type === 'sell', 'green-color': t.type === 'buy'}">
                                            @{{ t.price }}
                                        </td>
                                        <td>@{{ t.cbd }}</td>
                                        <td>@{{ t.crea }}</td>
                                    </tr>

                                </template>#}--}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{--{#User Open Orders#}--}}
                    <div v-if="session" class="col-12 order-md-4 order-lg-4">
                        <div class="boxed boxed--border">
                            <table id="user-orders" class="display" style="width:100%">
                                <thead>
                                <tr>
                                    <th>{{ __('lang.MARKET.DATE') }}</th>
                                    <th>{{ __('lang.MARKET.EXPIRATION') }}</th>
                                    <th>{{ __('lang.MARKET.TYPE') }}</th>
                                    <th>{{ __('lang.MARKET.PRICE') }}</th>
                                    <th>CREA</th>
                                    <th>CBD</th>
                                    <th>{{ __('lang.MARKET.ACTION') }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://www.amcharts.com/lib/4/core.js"></script>
        <script src="https://www.amcharts.com/lib/4/charts.js"></script>
        <script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="{{ asset('js/control/market.js') }}"></script>

        @include('layouts.modals')

    </div>
@endsection
