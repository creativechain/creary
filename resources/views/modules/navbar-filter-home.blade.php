<div class="col-6 col-md-2">
    <div class="dropdown">
        <span class="dropdown__trigger">Trigger Dropdown <i class="stack-down-open"></i></span>
        <div class="dropdown__container">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-lg-2 dropdown__content">
                        <ul class="list-inline navbar-followin-home">
                            <li class="list-inline-item w-100" v-if="session" v-bind:class="{'active': isUserFeed()}">
                                <a v-bind:href="'/@' + session.account.username + '/feed'">{{ __('lang.HOME.MENU_FOLLOWING') }}</a>
                            </li>
                            <li class="list-inline-item w-100" v-bind:class="{'active': nav === 'popular'}">
                                <a href="/popular" v-on:click="retrieveTrendingContent">{{ __('lang.HOME.MENU_POPULAR') }}</a>
                            </li>
                            <li class="list-inline-item w-100" v-bind:class="{'active': nav === 'skyrockets'}">
                                <a href="/skyrockets" v-on:click="retrieveHotContent">{{ __('lang.HOME.MENU_SKYROCKETS') }}</a>
                            </li>
                            <li class="list-inline-item w-100" v-bind:class="{'active': nav === 'now'}">
                                <a href="/now" v-on:click="retrieveNowContent">{{ __('lang.HOME.MENU_NOW') }}</a>
                            </li>
                            <li class="list-inline-item w-100" v-bind:class="{'active': nav === 'promoted'}">
                                <a href="/promoted" v-on:click="retrievePromotedContent">{{ __('lang.HOME.MENU_PROMOTED') }}</a>
                            </li>
                        </ul>
                    </div>
                </div><!--end row-->
            </div><!--end container-->
        </div><!--end dropdown container-->
    </div>
</div>
<div class="col-12 col-md-9">




    <div class="container">
        <div class="row">
            <div class="col">
                <div class="slider slider--inline-arrows slider--arrows-hover text-center" data-arrows="true">
                    <ul class="slides">
                        <li class="col-md-3 col-6">
                            <img alt="Image" class="image--xxs" src="img/partner-1.png" />
                        </li>
                        <li class="col-md-3 col-6">
                            <img alt="Image" class="image--xxs" src="img/partner-2.png" />
                        </li>
                        <li class="col-md-3 col-6">
                            <img alt="Image" class="image--xxs" src="img/partner-3.png" />
                        </li>
                        <li class="col-md-3 col-6">
                            <img alt="Image" class="image--xxs" src="img/partner-4.png" />
                        </li>
                        <li class="col-md-3 col-6">
                            <img alt="Image" class="image--xxs" src="img/partner-5.png" />
                        </li>
                        <li class="col-md-3 col-6">
                            <img alt="Image" class="image--xxs" src="img/partner-6.png" />
                        </li>
                    </ul>
                </div>
            </div>
            <!--end of col-->
        </div>
    </div>

</div>
<div class="col-1 col-md-1 text-right">
    <a href="" class="btn btn--sm button-filter">
        <svg xmlns="http://www.w3.org/2000/svg" width="13.458" height="8.145" viewBox="0 0 13.458 8.145" class="mr-2">
            <g id="Grupo_9843" data-name="Grupo 9843" transform="translate(-327.801 -77.38)">
                <path id="Trazado_9403" data-name="Trazado 9403" d="M-852.945,81.211h8.608" transform="translate(1185.597 0.241)" fill="none" stroke="#23221c" stroke-width="2"/>
                <path id="Trazado_9404" data-name="Trazado 9404" d="M-852.945,81.211h13.458" transform="translate(1180.746 -2.831)" fill="none" stroke="#23221c" stroke-width="2"/>
                <path id="Trazado_9405" data-name="Trazado 9405" d="M-852.945,81.211h4.3" transform="translate(1189.9 3.314)" fill="none" stroke="#23221c" stroke-width="2"/>
            </g>
        </svg>
    </a>
</div>


