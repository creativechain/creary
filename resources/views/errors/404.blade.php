@extends('layouts.error')

@section('content')
    <div class="main-container view-error-404">
        <section v-cloak id="error-container" class="height-100 bg--light text-center">
            <div class="container pos-vertical-center">
                <div class="row align-items-center">
                    <div class="col-md-6 text-left col-left">
                        <h1 class="h1--large">Oops!</h1>
                        <p class="sub-text"><span>404.</span>{{ __('lang.ERROR_PAGES.THATS_ERROR') }}</p>
                        <p>{{ __('lang.ERROR_PAGES.REQUESTED_URL') }} ...{{ request()->path() }} {{ __('lang.ERROR_PAGES.MESSAGE_404') }}</p>
                        <p>{{ __('lang.ERROR_PAGES.WE_KNOW') }}</p>
                    </div>
                    <div class="col-md-6">
                        <img src="/img/error/error.svg" alt="" />
                    </div>
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>
        <script src="{{ asset('js/control/error-page.js') }}"></script>
    </div>
@endsection
