<div class="row align-items-end align-items-center">
    <div class="col-6 col-md-2 order-0 order-md-0">
        <div class="dropdown">
            <span class="dropdown__trigger">Popular <i class="stack-down-open"></i></span>
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
    <div class="col-12 col-md-8 order-2 order-md-1 select-slider">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="slider slider--inline-arrows slider--arrows-hover text-center" data-arrows="true" data-autoplay="false">
                        <ul class="slides">
                            <li></li>
                            <li class="active"><a href="" >all</a></li>
                            <li><a href="">2D</a></li>
                            <li><a href="">3D</a></li>
                            <li><a href="">AR / VR</a></li>
                            <li><a href="">Advertising</a></li>
                            <li><a href="">Animation</a></li>
                            <li><a href="">Application design</a></li>
                            <li><a href="">Architecture</a></li>
                            <li><a href="">Artistic direction</a></li>
                            <li><a href="">Branding</a></li>
                            <li><a href="">Calligraphy</a></li>
                            <li><a href="">Ceramics</a></li>
                            <li><a href="">Character design</a></li>
                            <li><a href="">Cinematography</a></li>
                            <li><a href="">Collage</a></li>
                            <li><a href="">Comic</a></li>
                            <li><a href="">Conceptual art</a></li>
                            <li><a href="">Copywriting</a></li>
                            <li><a href="">Costume Design</a></li>
                            <li><a href="">Creative direction</a></li>
                            <li><a href="">Cryptoart</a></li>
                            <li><a href="">Design of products</a></li>
                            <li><a href="">Designs of objects or accessories</a></li>
                            <li><a href="">Designs with paper</a></li>
                            <li><a href="">Digital art</a></li>
                            <li><a href="">Digital paint</a></li>
                            <li><a href="">Display</a></li>
                            <li><a href="">Drafting</a></li>
                            <li><a href="">Editorial design</a></li>
                            <li><a href="">Engineering</a></li>
                            <li><a href="">Fashion design</a></li>
                            <li><a href="">Fine arts</a></li>
                            <li><a href="">Font design</a></li>
                            <li><a href="">GIF</a></li>
                            <li><a href="">Game design</a></li>
                            <li><a href="">Gastronomy</a></li>
                            <li><a href="">Graffiti</a></li>
                            <li><a href="">Graphic design</a></li>
                            <li><a href="">Handicrafts</a></li>
                            <li><a href="">Icon design</a></li>
                            <li><a href="">Illustration</a></li>
                            <li><a href="">Industrial design</a></li>
                            <li><a href="">Infographics</a></li>
                            <li><a href="">ink</a></li>
                            <li><a href="">Interactive layout</a></li>
                            <li><a href="">Interior design</a></li>
                            <li><a href="">Jewelry design</a></li>
                            <li><a href="">Journalism</a></li>
                            <li><a href="">Lettering</a></li>
                            <li><a href="">Logo design</a></li>
                            <li><a href="">Makers</a></li>
                            <li><a href="">Mockup</a></li>
                            <li><a href="">Mapping</a></li>
                            <li><a href="">Modeling</a></li>
                            <li><a href="">Movie theater</a></li>
                            <li><a href="">Music</a></li>
                            <li><a href="">Packaging</a></li>
                            <li><a href="">Painting</a></li>
                            <li><a href="">Performing arts</a></li>
                            <li><a href="">Photography</a></li>
                            <li><a href="">Photojournalism</a></li>
                            <li><a href="">Pixelart</a></li>
                            <li><a href="">Programming</a></li>
                            <li><a href="">Rendering</a></li>
                            <li><a href="">Retouching images</a></li>
                            <li><a href="">Scenography</a></li>
                            <li><a href="">Sculpture</a></li>
                            <li><a href="">Sound design</a></li>
                            <li><a href="">Street art</a></li>
                            <li><a href="">Styling</a></li>
                            <li><a href="">Textile design</a></li>
                            <li><a href="">Typography</a></li>
                            <li><a href="">UX / UI design</a></li>
                            <li><a href="">Visual effects</a></li>
                            <li><a href="">Web design</a></li>
                            <li><a href="">Wood work</a></li>
                            <li><a href="">Writing</a></li>
                        </ul>
                    </div>
                </div>
                <!--end of col-->
            </div>
        </div>

    </div>
    <div class="col-6 col-md-2 text-right order-1 order-md-2">
        <a href="#" class="btn btn--sm button-filter" value="show/hide">
            <svg xmlns="http://www.w3.org/2000/svg" width="13.458" height="8.145" viewBox="0 0 13.458 8.145" class="mr-2">
                <g id="Grupo_9843" data-name="Grupo 9843" transform="translate(-327.801 -77.38)">
                    <path id="Trazado_9403" data-name="Trazado 9403" d="M-852.945,81.211h8.608" transform="translate(1185.597 0.241)" fill="none" stroke="#23221c" stroke-width="2"/>
                    <path id="Trazado_9404" data-name="Trazado 9404" d="M-852.945,81.211h13.458" transform="translate(1180.746 -2.831)" fill="none" stroke="#23221c" stroke-width="2"/>
                    <path id="Trazado_9405" data-name="Trazado 9405" d="M-852.945,81.211h4.3" transform="translate(1189.9 3.314)" fill="none" stroke="#23221c" stroke-width="2"/>
                </g>
            </svg> Filter
        </a>
    </div>
</div>



<div class="row mt-3 row-filter-select" style="display: none;">
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">Licenses</span>
        <div class="dropdown">
            <span class="dropdown__trigger filter"><i class="stack-down-open"></i> Trigger Dropdown </span>
            <div class="dropdown__container">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 dropdown__content p-0">
                            <ul class="ul-select">
                                <li><a href="">All</a></li>
                                <li><a href="">Free downloads</a></li>
                                <li><a href="">Payment downloads</a></li>
                            </ul>
                        </div>
                    </div><!--end row-->
                </div><!--end container-->
            </div><!--end dropdown container-->
        </div>
    </div>
    <div class="col-12 col-md-6 mt-3 mt-md-0">
        <span class="font-weight-bold">Downloads</span>
        <div class="dropdown">
            <span class="dropdown__trigger filter">Trigger Dropdown <i class="stack-down-open"></i></span>
            <div class="dropdown__container">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 dropdown__content  p-0">
                            <ul class="ul-select">
                                <li><a href="">All</a></li>
                                <li><a href="">Free downloads</a></li>
                                <li><a href="">Payment downloads</a></li>
                            </ul>
                        </div>
                    </div><!--end row-->
                </div><!--end container-->
            </div><!--end dropdown container-->
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        $('.button-filter').on('click', function(){
            $('.row-filter-select').fadeToggle('show');
        });
    })
</script>
