<section v-cloak v-if="showBanner" class="imagebg image--dark cover cover-blocks" id="slide-home" style="background-color: #F3604C;">
    <div class="row-close" v-on:click="showBanner = false">
        <a class="cursor"><i class="fas fa-times"></i></a>
    </div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-md-8 text-center text-md-left">
                <h1 v-html="'{{ __('lang.BANNER.TITLE') }}'"></h1>
                <h3 class="font-weight-normal">{{ __('lang.BANNER.SUBTITLE') }}</h3>
                <a href="/welcome" class="btn btn--sm btn-primary">
                    <span class="btn__text font-weight-bold">
                            {{ __('lang.BUTTON.SIGN_UP') }}
                    </span>
                </a>
            </div>
            <div class="col-12 col-md-4 text-center text-md-left">
                <img alt="background" src="{{ asset('img/crea-web/banner-creary-illworks.png') }}"/>
                <div class="firma-banner">
                    <a href="/@illworks"><span style="color: white">Art by Chris Davis</span></a>
                </div>
            </div>
        </div>
    </div>
</section>
