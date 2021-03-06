<div class="row align-items-end align-items-center">
    <div class="col-6 col-md-2 order-0 order-md-0">
        <div id="category-select" class="dropdown dropdown-popular">
            <span class="dropdown__trigger">@{{ getFilterText() }} <i class="stack-down-open"></i></span>
            <div class="dropdown__container nav-filter-items">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3 col-lg-2 dropdown__content">
                            <ul class="list-inline navbar-followin-home">
                                <li class="list-inline-item w-100" v-if="session" v-bind:class="{'active': isUserFeed()}">
                                    <a v-bind:href="'/@' + session.account.username + '/feed'"
                                       v-on:click="onSelectCategory($event, 'feed')">{{ __('lang.FILTER.FEED') }}</a>
                                </li>
                                <li class="list-inline-item w-100" v-bind:class="{'active': category === 'popular'}">
                                    <a href="/popular" v-on:click="onSelectCategory($event, 'popular')">{{ __('lang.FILTER.POPULAR') }}</a>
                                </li>
                                <li class="list-inline-item w-100" v-bind:class="{'active': category === 'skyrockets'}">
                                    <a href="/skyrockets" v-on:click="onSelectCategory($event, 'skyrockets')">{{ __('lang.FILTER.SKYROCKETS') }}</a>
                                </li>
                                <li class="list-inline-item w-100" v-bind:class="{'active': category === 'now'}">
                                    <a href="/now" v-on:click="onSelectCategory($event, 'now')">{{ __('lang.FILTER.NOW') }}</a>
                                </li>
                                <li class="list-inline-item w-100" v-bind:class="{'active': category === 'promoted'}">
                                    <a href="/promoted" v-on:click="onSelectCategory($event, 'promoted')">{{ __('lang.FILTER.PROMOTED') }}</a>
                                </li>
                            </ul>
                        </div>
                    </div><!--end row-->
                </div><!--end container-->
            </div><!--end dropdown container-->
        </div>
    </div>
    <div class="col-12 col-md-8 order-2 order-md-1 select-slider">
        <div class="container">
            <div class="row">
                <div class="col" >
                    <div class="filter-category">
                        <div class="actions action-prev -hide">
                            <a href="#">
                                <svg class="action-icon" viewBox="0 0 100 100">
                                    <path d="M 10,50 L 60,100 L 70,90 L 30,50  L 70,10 L 60,0 Z"></path>
                                </svg>
                            </a>
                        </div>
                        <div class="actions action-next -hide">
                            <a href="#">
                                <svg class="action-icon" viewBox="0 0 100 100">
                                    <path d="M 10,50 L 60,100 L 70,90 L 30,50  L 70,10 L 60,0 Z" transform="translate(100, 100) rotate(180) "></path>
                                </svg>
                            </a>
                        </div>
                        <ul class="filter-list">
                            <li v-bind:class="{ active: (!discuss && !search) || (discuss === 'feed' && !search) }" class="category">
                                <a v-bind:href="linkForTag()" >{{ __('lang.FILTER.ALL') }}</a>
                            </li>
                            <li v-for="t in discussions" class="category" v-bind:class="{ active: (discuss === t.name || search === t.name) }">
                                <a  class="text-capitalize"
                                    v-bind:href="linkForTag(t)"
                                    v-on:click="onSelectDiscuss($event, t.name)">
                                    @{{ t.name }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-6 col-md-2 text-right order-1 order-md-2">
        <a href="#" class="btn btn--sm button-filter" v-bind:class="{ disabled: !enableFilter() }" value="show/hide" v-bind:aria-disabled="!enableFilter()">
            <svg xmlns="http://www.w3.org/2000/svg" width="13.458" height="8.145" viewBox="0 0 13.458 8.145" class="mr-2">
                <g id="Grupo_9843" data-name="Grupo 9843" transform="translate(-327.801 -77.38)">
                    <path id="Trazado_9403" data-name="Trazado 9403" d="M-852.945,81.211h8.608" transform="translate(1185.597 0.241)" fill="none" stroke="#23221c" stroke-width="2"/>
                    <path id="Trazado_9404" data-name="Trazado 9404" d="M-852.945,81.211h13.458" transform="translate(1180.746 -2.831)" fill="none" stroke="#23221c" stroke-width="2"/>
                    <path id="Trazado_9405" data-name="Trazado 9405" d="M-852.945,81.211h4.3" transform="translate(1189.9 3.314)" fill="none" stroke="#23221c" stroke-width="2"/>
                </g>
            </svg>
            {{ __('lang.FILTER.FILTER') }}
        </a>
    </div>
</div>

<div class="row mt-3 row-filter-select" style="display: none;">
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{ __('lang.FILTER.LICENSES') }}</span>
        <div class="input-select select-filter">
            <select v-model="license" v-on:change="onSelectLicense">
                <option v-bind:value="null">{{ __('lang.FILTER.ALL') }}</option>
                <option v-for="l in availableLicenses" v-bind:value="l.flag">
                    @{{ l.name }}
                </option>
            </select>
        </div>

<!--        <div class="dropdown">
            <span class="dropdown__trigger filter"><i class="stack-down-open"></i> @{{ license ? license.name : lang.FILTER.ALL }} </span>
            <div class="dropdown__container">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 dropdown__content p-0">
                            <ul class="ul-select">
                                <li>
                                    <a href="" v-on:click="onSelectLicense($event, null)">
                                        {{ __('lang.FILTER.ALL') }}
                                    </a>
                                </li>
                                <li v-for="l in availableLicenses">
                                    <a href="" v-on:click="onSelectLicense($event, l)">
                                        <img v-bind:src="l.getIcon()">
                                        @{{ l.name }}
                                    </a>
                                </li>
&lt;!&ndash;                                <li>
                                    <a href=""><img src="/img/icons/license/copyright.png" alt="">CopyRight</a>
                                </li>
                                <li><a href=""><img src="/img/icons/license/creativecommons.png" alt="">Creative Commons</a></li>
                                <li><a href=""><img src="/img/icons/license/freecontent.png" alt="">Free Content</a></li>&ndash;&gt;
                            </ul>
                        </div>
                    </div>&lt;!&ndash;end row&ndash;&gt;
                </div>&lt;!&ndash;end container&ndash;&gt;
            </div>&lt;!&ndash;end dropdown container&ndash;&gt;
        </div>-->
    </div>

    <div class="col-12 col-md-6 mt-3 mt-md-0">
        <span class="font-weight-bold">{{ __('lang.FILTER.DOWNLOADS') }}</span>
        <div class="input-select select-filter">
            <select v-model="download" v-on:change="onSelectDownload">
                <option v-bind:value="null" >{{ __('lang.FILTER.ALL') }}</option>
                <option value="free" >{{ __('lang.FILTER.FREE_DOWNLOADS') }}</option>
                <option value="paid" >{{ __('lang.FILTER.PAYMENT_DOWNLOADS') }}</option>
            </select>
        </div>
<!--        <div class="dropdown">
            <span class="dropdown__trigger filter">@{{ download ? download : lang.FILTER.ALL }} <i class="stack-down-open"></i></span>
            <div class="dropdown__container">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 dropdown__content  p-0">
                            <ul class="ul-select">
                                <li><a href="" v-on:click="onSelectDownload($event, null)">{{ __('lang.FILTER.ALL') }}</a></li>
                                <li><a href="" v-on:click="onSelectDownload($event, 'free')">{{ __('lang.FILTER.FREE_DOWNLOADS') }}</a></li>
                                <li><a href="" v-on:click="onSelectDownload($event, 'paid')">{{ __('lang.FILTER.PAYMENT_DOWNLOADS') }}</a></li>
                            </ul>
                        </div>
                    </div>&lt;!&ndash;end row&ndash;&gt;
                </div>&lt;!&ndash;end container&ndash;&gt;
            </div>&lt;!&ndash;end dropdown container&ndash;&gt;
        </div>-->
    </div>
</div>
