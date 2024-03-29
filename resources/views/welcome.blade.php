@extends('layouts.app')

@section('content')
    <script src="https://www.google.com/recaptcha/api.js?render=6Lch-SodAAAAAOe-mD562Y8-sbcT56byW7XsC0cy"></script>

    <div v-cloak id="welcome" class="main-container view-welcome">

        {{--<!-- Slide 1-->--}}
        <section v-bind:class="{ hidden: slide !== 1, imageblock: true, switchable: true, 'height-100': true }">
            <div class="imageblock__content col-lg-6 col-md-6 col-sm-12 pos-right">
                <div class="background-image-holder">
                    <img alt="image" src="{{ asset('img/welcome/creary_slide_1@2x.jpg') }}" class="logo-welcome" />
                </div>
            </div>
            <div class="container pos-vertical-center content-slide-welcome">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="welcome-content">
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 ">
                                    <img class="logo-welcome" src="{{ asset('img/welcome/logo-welcome.png') }}" alt="" />

                                    <h1>{{ __('lang.WELCOME.SLIDE1_TITLE') }}</h1>
                                    <p class="lead">{{ __('lang.WELCOME.SLIDE1_TEXT1') }}</p>
                                    <p class="lead">{{ __('lang.WELCOME.SLIDE1_TEXT2') }}</p>
                                    <p class="lead">{{ __('lang.WELCOME.SLIDE1_TEXT3') }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center">
                                    <div class="row justify-content-center">
                                        <div class="col-6">
                                            <div id="welcome-slide1-button-signup" class="btn btn--primary w-100" v-on:click="changeSlide(2)">
                                            <span class="btn__text">
                                                {{ __('lang.BUTTON.SIGN_UP') }}
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="text-center ">
                                <ul class="list-inline list-unstyled ul-row-pagination">
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome"></p></li>
                                    <li><p class="circle-welcome"></p></li>
                                    <li><p class="circle-welcome"></p></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{--<!-- Slide 2-->--}}
        <section v-bind:class="{ hidden: slide !== 2, imageblock: true, switchable: true, 'height-100': true }">
            <div class="imageblock__content col-lg-6 col-md-6 col-sm-12 pos-right">
                <div class="background-image-holder">
                    <img alt="image" src="{{ asset('img/welcome/creary_slide_2@2x.jpg') }}" class="logo-welcome" />
                </div>
            </div>
            <div class="container pos-vertical-center content-slide-welcome">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="welcome-content">
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 ">
                                    <img class="logo-welcome" src="{{ asset('img/welcome/logo-welcome.png') }}" alt="" />
                                    <h1>{{ __('lang.WELCOME.SLIDE2_TITLE') }}</h1>
                                    <p class="lead">{{ __('lang.WELCOME.SLIDE2_TEXT1') }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center row-inputs">
                                    <input ref="inputusername" v-bind:class="{ 'validate-required': true, 'field-error': error.username && error.username.length > 0 }" type="text" placeholder="{{ __('lang.WELCOME.SLIDE2_INPUT_PLACEHOLDER') }}" />
                                    <span v-if="error.username && error.username.length > 0" class="error-color-form">@{{ error.username }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center">
                                    <div class="row justify-content-center">
                                        <div class="col-6">
                                            <div class="btn btn--primary w-100" v-on:click="changeSlide(3, error.username)">
                                            <span class="btn__text">
                                                {{ __('lang.BUTTON.CONTINUE') }}
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="text-center">
                                <ul class="list-inline list-unstyled ul-row-pagination">
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome"></p></li>
                                    <li><p class="circle-welcome"></p></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end of row-->
            </div>
            <!--end of container-->
        </section>

        {{--<!-- Slide 3-->--}}
        <section v-bind:class="{ hidden: slide !== 3, imageblock: true, switchable: true, 'height-100': true }">
            <div class="imageblock__content col-lg-6 col-md-6 col-sm-12 pos-right">
                <div class="background-image-holder">
                    <img alt="image" src="{{ asset('img/welcome/creary_slide_3@2x.jpg') }}" class="logo-welcome" />
                </div>
            </div>
            <div class="container pos-vertical-center content-slide-welcome ">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="welcome-content">
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 ">
                                    <img class="logo-welcome" src="{{ asset('img/welcome/logo-welcome.png') }}" alt="" />
                                    <h1>{{ __('lang.WELCOME.SLIDE3_TITLE') }}</h1>
                                    <p class="lead">{{ __('lang.WELCOME.SLIDE3_TEXT1') }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center row-inputs">
                                    <input id="reg-email" ref="regemail" type="email" v-bind:class="{ 'validate-required': true, 'field-error': error.email && error.email.length > 0 }" placeholder="{{ __('lang.WELCOME.SLIDE3_INPUT_PLACEHOLDER') }}" />
                                    <span v-if="error.email && error.email.length > 0" class="error-color-form">@{{ error.email }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center">
                                    <div class="row">
                                        <div class="col-md-6 col-6">
                                            <div class="btn btn--transparent w-100" v-on:click="changeSlide(2, null)">
                                            <span id="welcome-slide3-button-back" class="btn__text">
                                                {{ __('lang.BUTTON.GO_BACK') }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="btn btn--primary w-100" v-on:click="changeSlide(4, null)">
                                            <span id="welcome-slide3-button-continue" class="btn__text">
                                                {{ __('lang.BUTTON.CONTINUE') }}
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="text-center">
                                <ul class="list-inline list-unstyled ul-row-pagination">
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome"></p></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end of row-->
            </div>
            <!--end of container-->
        </section>

        {{--<!-- Slide 4-->--}}
        <section v-bind:class="{ hidden: slide !== 4, imageblock: true, switchable: true, 'height-100': true }">
            <div class="imageblock__content col-lg-6 col-md-6 col-sm-12 pos-right">
                <div class="background-image-holder">
                    <img alt="image" src="{{ asset('img/welcome/creary_slide_4@2x.jpg') }}" class="logo-welcome" />
                </div>
            </div>
            <div class="container pos-vertical-center content-slide-welcome ">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="welcome-content">
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 ">
                                    <img class="logo-welcome" src="{{ asset('img/welcome/logo-welcome.png') }}" alt="" />
                                    <h1>{{ __('lang.WELCOME.SLIDE4_TITLE') }}</h1>
                                    <p class="lead">{{ __('lang.WELCOME.SLIDE4_TEXT1') }}</p>
                                </div>
                            </div>

                        </div>
                        <div class="row  justify-content-center">
                            <div class="text-center">
                                <ul class="list-inline list-unstyled ul-row-pagination">
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{--<!-- Slide 5-->--}}
        <section v-bind:class="{ hidden: slide !== 5, imageblock: true, switchable: true, 'height-100': true }">
            <div class="imageblock__content col-lg-6 col-md-6 col-sm-12 pos-right">
                <div class="background-image-holder">
                    <img alt="image" src="{{ asset('img/welcome/creary_slide_5@2x.jpg') }}" class="logo-welcome" />
                </div>
            </div>
            <div class="container pos-vertical-center content-slide-welcome ">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="welcome-content">
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 ">
                                    <img class="logo-welcome" src="{{ asset('img/welcome/logo-welcome.png') }}" alt="" />
                                    <h1>{{ __('lang.WELCOME.SLIDE5_TITLE') }} @{{ username }}!</h1>
                                    <p class="lead">{{ __('lang.WELCOME.SLIDE5_TEXT1') }}</p>
                                </div>
                            </div>
                            <div class="row mt--1">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center">
                                    <div class="row justify-content-center">
                                        <div class="col-6">
                                            <div class="btn btn--primary w-100" v-on:click="changeSlide(6)">
                                            <span class="btn__text">
                                                {{ __('lang.BUTTON.CONTINUE') }}
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="text-center">
                                <ul class="list-inline list-unstyled ul-row-pagination">
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome "></p></li>
                                    <li><p class="circle-welcome "></p></li>
                                    <li><p class="circle-welcome "></p></li>
                                    <li><p class="circle-welcome "></p></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{--<!-- Slide 6-->--}}
        <section v-bind:class="{ hidden: slide !== 6, imageblock: true, switchable: true, 'height-100': true }">
            <div class="imageblock__content col-lg-6 col-md-6 col-sm-12 pos-right">
                <div class="background-image-holder">
                    <img alt="image" src="{{ asset('img/welcome/creary_slide_6-sms.jpg') }}" class="logo-welcome" />
                </div>
            </div>
            <div class="container pos-vertical-center content-slide-welcome ">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="welcome-content">
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 ">
                                    <img class="logo-welcome" src="{{ asset('img/welcome/logo-welcome.png') }}" alt="" />
                                    <h1>{{ __('lang.WELCOME.SLIDE6_TITLE') }}</h1>
                                    <p class="lead">{{ __('lang.WELCOME.SLIDE6_TEXT1') }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 offset-lg-1 col-md-6 text-center row-inputs">
                                    <label>{{ __('lang.WELCOME.SLIDE6_SELECT_COUNTRY') }}</label>
                                    <autocomplete
                                        v-bind:items="countryCodes"
                                        placeholder="Select country prefix"
                                        v-on:item="onPhonePrefix"
                                    >
                                    </autocomplete>

                                </div>
                                <div class="col-lg-6 offset-lg-1 col-md-6 text-center row-inputs">
                                    <input id="reg-phone" type="phone" v-model="phone" v-bind:class="{ 'validate-required': true, 'field-error': error.phone && error.phone.length > 0 }" placeholder="{{ __('lang.WELCOME.SLIDE6_INPUT_PLACEHOLDER') }}" />
                                    <span v-if="error.phone && error.phone.length > 0" class="error-color-form">@{{ error.phone }}</span>
                                </div>
                            </div>

                            <div v-if="sentSMS" class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center row-inputs">
                                    <input id="reg-phone-code" type="text" v-model="phone_code" v-bind:class="{ 'validate-required': true, 'field-error': error.phone_code && error.phone_code.length > 0 }" placeholder="{{ __('lang.WELCOME.SLIDE6_INPUT_PLACEHOLDER2') }}" />
                                    <span v-if="error.phone_code && error.phone_code.length > 0" class="error-color-form">@{{ error.phone_code }}</span>
                                </div>
                            </div>

                            <div class="row mt--1">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center">
                                    <div class="row justify-content-center">
                                        <div v-if="!sentSMS" class="col-6">
                                            <div  class="btn btn--primary w-100" v-on:click="checkPhone()">
                                            <span class="btn__text">
                                                {{ __('lang.BUTTON.CONTINUE') }}
                                            </span>
                                            </div>
                                        </div>
                                        <div  v-else>
                                            <div class="col-md-6 col-6">
                                                <div class="btn btn--transparent w-100" v-on:click="checkPhone()">
                                                    <span class="btn__text">
                                                        {{ __('lang.BUTTON.SEND_CODE_AGAIN') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="btn btn--primary w-100" v-on:click="verifyPhone()">
                                                    <span class="btn__text">
                                                        {{ __('lang.BUTTON.VERIFY_PHONE') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="text-center">
                                <ul class="list-inline list-unstyled ul-row-pagination">
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome "></p></li>
                                    <li><p class="circle-welcome "></p></li>
                                    <li><p class="circle-welcome "></p></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{--<!-- Slide 7-->--}}
        <section v-bind:class="{ hidden: slide !== 7, imageblock: true, switchable: true, 'height-100': true }">
            <div class="imageblock__content col-lg-6 col-md-6 col-sm-12 pos-right">
                <div class="background-image-holder">
                    <img alt="image" src="{{ asset('img/welcome/creary_slide_6-7@2x.jpg') }}" class="logo-welcome" />
                </div>
            </div>
            <div class="container pos-vertical-center content-slide-welcome ">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="welcome-content">
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 ">
                                    <img class="logo-welcome" src="{{ asset('img/welcome/logo-welcome.png') }}" alt="" />

                                    <h1>{{ __('lang.WELCOME.SLIDE7_TITLE') }}</h1>
                                    <p class="lead" v-html="'{{ __('lang.WELCOME.SLIDE7_TEXT1') }}'"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center row-inputs">
                                    <input id="welcome-slide7-input" v-on:input="inputPassword" v-bind:class="{ 'validate-required': true, 'field-error': error.password }" type="text" v-bind:value="suggestedPassword" placeholder="{{ __('lang.WELCOME.SLIDE7_INPUT_PLACEHOLDER') }}" />
                                    <span v-if="error.password" class="error-color-form">@{{ error.password }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center row-inputs">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="btn btn--transparent w-100" v-on:click="copyToClipboard('welcome-slide7-input')">
                                            <span class="btn__text btn_copy">
                                                {{ __('lang.BUTTON.COPY_PASSWORD') }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="btn btn--black w-100" v-on:click="suggestPassword()">
                                            <span class="btn__text color--white">
                                                {{ __('lang.BUTTON.NEW_PASSWORD') }}
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 offset-md-3">
                                            <div class="btn btn--primary w-100" v-on:click="changeSlide(8, error.password)">
                                            <span class="btn__text">
                                               {{ __('lang.BUTTON.CONTINUE') }}
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="text-center">
                                <ul class="list-inline list-unstyled ul-row-pagination">
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome "></p></li>
                                    <li><p class="circle-welcome "></p></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{--<!-- Slide 8-->--}}
        <section v-bind:class="{ hidden: slide !== 8, imageblock: true, switchable: true, 'height-100': true }">
            <div class="imageblock__content col-lg-6 col-md-6 col-sm-12 pos-right">
                <div class="background-image-holder">
                    <img alt="image" src="{{  asset('img/welcome/creary_slide_6-7@2x.jpg') }}" class="logo-welcome" />
                </div>
            </div>
            <div class="container pos-vertical-center content-slide-welcome ">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="welcome-content form-terms">
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 ">
                                    <img class="logo-welcome" src="{{ asset('img/welcome/logo-welcome.png') }}" alt="" />
                                    <h1>{{ __('lang.WELCOME.SLIDE8_TITLE') }}</h1>
                                    <p class="lead">{{ __('lang.WELCOME.SLIDE8_TEXT1') }}</p>
                                    <p class="lead">{{ __('lang.WELCOME.SLIDE8_TEXT2') }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center row-inputs">
                                    <input v-on:input="inputCheckPassword" v-bind:class="{ 'validate-required': true, 'field-error': error.matchPassword }" type="text" name="My Input" placeholder="{{ __('lang.WELCOME.SLIDE8_INPUT_PLACEHOLDER') }}" />
                                    <span v-if="error.matchPassword" class="error-color-form">@{{ error.matchPassword }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 col-terms">
                                   <div class="row-checkbox-term">
                                       <div class="input-checkbox">
                                           <input id="welcome-check-terms" v-model="checkedTerms" type="checkbox" />
                                           <label for="welcome-check-terms"></label>
                                       </div>
                                       <span v-bind:class="{ 'error-color-form': !checkedTerms }"></span>
                                        <a href="/terms_and_conditions" target="_blank">{{ __('lang.WELCOME.SLIDE8_CHECKBOX1') }}</a>
                                   </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 col-policy">
                                    <div class="input-checkbox">
                                        <input v-model="checkedPolicy" id="welcome-check-policy" type="checkbox" name="agree_policy" />
                                        <label for="welcome-check-policy"></label>
                                    </div>
                                    <span v-bind:class="{ 'error-color-form': !checkedPolicy }"></span>
                                    <a href="/privacy_policy" target="_blank">{{ __('lang.WELCOME.SLIDE8_CHECKBOX2') }}</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center">
                                    <div class="row justify-content-center">
                                        <div class="col-6">
                                            <div class="btn btn--primary w-100" v-on:click="createAccount()">
                                            <span class="btn__text">
                                                {{ __('lang.BUTTON.CREATE_ACCOUNT') }}
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="text-center">
                                <ul class="list-inline list-unstyled ul-row-pagination">
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome "></p></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{--<!-- Slide 9-->--}}
        <section v-bind:class="{ hidden: slide !== 9, imageblock: true, switchable: true, 'height-100': true }">
            <div class="imageblock__content col-lg-6 col-md-6 col-sm-12 pos-right">
                <div class="background-image-holder">
                    <img alt="image" src="{{ asset('img/welcome/creary_slide_8@2x.jpg') }}" class="logo-welcome" />
                </div>
            </div>
            <div class="container pos-vertical-center content-slide-welcome ">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="welcome-content">
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 ">
                                    <img class="logo-welcome" src="{{ asset('img/welcome/logo-welcome.png') }}" alt="" />
                                    <h1>{{ __('lang.WELCOME.SLIDE9_TITLE') }}</h1>
                                    <p class="lead">{{ __('lang.WELCOME.SLIDE9_TEXT1') }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 offset-lg-1 col-md-12 text-center">
                                    <div class="row justify-content-center">
                                        <div class="col-6">
                                            <a class="btn btn--primary w-100" href="/">
                                            <span class="btn__text">
                                                {{ __('lang.BUTTON.CONTINUE') }}
                                            </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="text-center">
                                <ul class="list-inline list-unstyled ul-row-pagination">
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                    <li><p class="circle-welcome active"></p></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
    @include('layouts.modals')
    <script>
        window.countryCodes = {!! \Illuminate\Support\Facades\Storage::disk('local')->get('country_codes.json') !!};
    </script>
    <script src="{{ asset('js/control/welcome.js') }}"></script>
@endsection
