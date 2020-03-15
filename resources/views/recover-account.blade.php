@extends('layouts.app')

@section('content')
    <div class="main-container view-recorver-account">
        <section v-cloak id="recover-container" class="space--sm">
            <div class="container post-container-home">
                <div class="col-md-8 offset-2">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="title-explorer">{{ __('lang.ACCOUNT_RECOVERY.TITLE') }}</h3>
                        </div>
                    </div>
                    <form class="row">
                        <div class="col-md-12">
                            <p class="lead">{{ __('lang.ACCOUNT_RECOVERY.DESCRIPTION_1') }}</p>
                            <p class="lead">{{ __('lang.ACCOUNT_RECOVERY.DESCRIPTION_2') }}</p>
                            <p class="lead">{{ __('lang.ACCOUNT_RECOVERY.DESCRIPTION_3') }}</p>
                            <p class="lead">{{ __('lang.ACCOUNT_RECOVERY.DESCRIPTION_4') }}</p>
                        </div>
                        <div class="col-md-12 mt-4">
                            <label class="lead mb-0">{{ __('lang.ACCOUNT_RECOVERY.INPUT_USERNAME') }}:</label>
                            <input type="text" name="name" placeholder="{{ __('lang.ACCOUNT_RECOVERY.INPUT_USERNAME') }}" class="validate-required" />
                        </div>
                        <div class="col-md-12">
                            <label class="lead mb-0">Email Address:</label>
                            <input type="email" name="email" placeholder="{{ __('lang.ACCOUNT_RECOVERY.INPUT_EMAIL') }}" class="validate-required" />
                        </div>
                        <div class="col-md-12 mt-3">
                            <a id="welcome-slide1-button-signup" href="#" class="btn btn--primary">
                            <span class="btn__text">
                                Sign up
                            </span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <script src="{{ asset('js/recover-account.js') }}"></script>

        @include('layouts.modals')

    </div>

@endsection
