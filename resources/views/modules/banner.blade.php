<section v-cloak v-if="showBanner" class="imagebg image--dark cover cover-blocks bg--secondary" id="slide-home">
    <div class="row-close" v-on:click="showBanner = false">
        <a class="cursor"><i class="fas fa-times"></i></a>
    </div>
    <div class="background-image-holder">
        <img alt="background" src="{{ asset('img/crea-web/banner_2_6_casmiclab.jpg') }}"/>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-lg-6 offset-1">
                <h1>{{ __('lang.BANNER.TITLE') }}</h1>
                <h3>{{ __('lang.BANNER.SUBTITLE') }}</h3>
                <a href="/welcome" class="btn btn--sm">
                    <span class="btn__text font-weight-bold">
                            {{ __('lang.BUTTON.SIGN_UP') }}
                    </span>
                </a>
            </div>
        </div>
    </div>
</section>
