<div class="modal-instance ">
    <div id="modal-alert" class="modal-container modal-report" v-bind:class="{'modal-active': show}" style="z-index: 10000">
        <div class="modal-content">
            <section class="unpad ">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="boxed boxed--lg bg--white feature">
                                <div class="modal-close modal-close-cross"></div>
                                <h3>@{{ config.title }}</h3>
                                <div >

                                    <template v-for="b in config.body">
                                        <p v-html="linkfy(b)"></p>
                                    </template>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12 text-right">
                                        <div class="btn btn--primary" v-on:click="closeModal">
                                            <span class="btn__text ">
                                                {{ __('lang.BUTTON.CLOSE') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>
        </div>
    </div>
</div>

<div class="modal-instance">
    <div id="modal-role" class="modal-container" v-bind:class="{'modal-active': show}" style="z-index: 1000">
        <div class="modal-content section-modal">
            <section class="unpad ">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="boxed boxed--lg bg--white text-center feature">
                                <div class="modal-close modal-close-cross"></div>
                                <h3>@{{ lang.AUTH_MODAL.TITLE.replace("%s", role) }}</h3>
                                <div class="feature__body">
                                    <form action="#0" v-on:submit="fetchKey" class="content-login">
                                        <div class="row">
                                            <div class="col-md-12 text-left">
                                                <input v-model="inputs.username.value"
                                                       v-on:input="checkUsername"
                                                       type="text" placeholder="{{ __('lang.LOGIN.USERNAME') }}"
                                                       disabled />
                                                <span class="error-color-form">@{{ inputs.username.error || "" }}</span>
                                            </div>
                                            <div class="col-md-12 text-left">
                                                <input v-model="inputs.password.value"
                                                       type="password" placeholder="{{ __('lang.AUTH_MODAL.INPUT_PASSWORD') }}"/>
                                                <span class="error-color-form">@{{ inputs.password.error || "" }}</span>
                                            </div>
                                            <div class="col m-2">
                                                <a class="btn btn--transparent w-100" href="#" v-on:click="closeModal">
                                                    <span class="btn__text color--dark font-weight-bold">{{ __('lang.BUTTON.CANCEL') }}</span>
                                                </a>
                                            </div>
                                            <div class="col m-2">
                                                <a class="btn btn--primary w-100" href="#" v-on:click="fetchKey">
                                                    <span class="btn__text font-weight-bold">{{ __('lang.BUTTON.LOGIN') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                        <!--end of row-->
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>
        </div>
    </div>
</div>
