@include('element/navbar')
<div class="main-container view-error-404">
    <section v-cloak id="error-container" class="height-100 bg--light text-center">
        <div class="container pos-vertical-center">
            <div class="row align-items-center">
                <div class="col-md-6 text-left col-left">
                    <h1 class="h1--large">Sorry!</h1>
                    <p class="sub-text"><span>503.</span> {{ __('lang.ERROR_PAGES.MAINTENANCE') }}</p>
                    <p>{{ __('lang.ERROR_PAGES.MESSAGE_503_1') }}</p>
                    <p>{{ __('lang.ERROR_PAGES.MESSAGE_503_2') }}</p>
                </div>
                <div class="col-md-6">
                    <img src="/img/creary_manteniment.svg" alt="" />
                </div>
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <script src="{{ asset('js/error-page.js') }}"></script>
    @include('element/footer')
