<div v-pre class="modal-instance">
    <div id="modal-report" data-modal-id="modal-report" class="modal-container modal-report">
        <div class="modal-content">
            <section class="unpad ">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="boxed boxed--lg bg--white feature">
                                <div class="modal-close modal-close-cross"></div>
                                <h3>{{ __('lang.PUBLICATION.MODAL_REPORT_TITLE') }}</h3>
                                <div class="feature__body">
                                    <p class="mb-0">{{ __('lang.PUBLICATION.MODAL_REPORT_HEAD1') }}</p>
                                    <p>{{ __('lang.PUBLICATION.MODAL_REPORT_HEAD2') }}</p>
                                    <ul>
                                        <li><p>- {{ __('lang.PUBLICATION.MODAL_REPORT_REASON1') }}</p></li>
                                        <li><p>- {{ __('lang.PUBLICATION.MODAL_REPORT_REASON2') }}</p></li>
                                        <li><p>- {{ __('lang.PUBLICATION.MODAL_REPORT_REASON3') }}</p></li>
                                        <li><p>- {{ __('lang.PUBLICATION.MODAL_REPORT_REASON4') }}</p></li>
                                    </ul>
                                    <p>{{ __('lang.PUBLICATION.MODAL_REPORT_FOOTER') }}</p>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12 text-right">
                                        <div class="btn btn--primary modal-close" v-on:click="vote(-10000)">
                                            <span class="btn__text">
                                                {{ __('lang.BUTTON.REPORT') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<div v-pre class="modal-instance">
    <div id="modal-report-comment" data-modal-id="modal-report-comment" class="modal-container modal-report">
        <div class="modal-content">
            <section class="unpad ">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="boxed boxed--lg bg--white feature">
                                <div class="modal-close modal-close-cross"></div>
                                <h3>{{ __('lang.PUBLICATION.MODAL_REPORT_COMMENT_TITLE') }}</h3>
                                <div class="feature__body">
                                    <p class="mb-0">{{ __('lang.PUBLICATION.MODAL_REPORT_COMMENT_HEAD1') }}</p>
                                    <p>{{ __('lang.PUBLICATION.MODAL_REPORT_HEAD2') }}</p>
                                    <ul>
                                        <li><p>- {{ __('lang.PUBLICATION.MODAL_REPORT_COMMENT_REASON1') }}</p></li>
                                        <li><p>- {{ __('lang.PUBLICATION.MODAL_REPORT_COMMENT_REASON2') }}</p></li>
                                        <li><p>- {{ __('lang.PUBLICATION.MODAL_REPORT_COMMENT_REASON3') }}</p></li>
                                        <li><p>- {{ __('lang.PUBLICATION.MODAL_REPORT_COMMENT_REASON4') }}</p></li>
                                    </ul>
                                    <p>{{ __('lang.PUBLICATION.MODAL_REPORT_COMMENT_FOOTER') }}</p>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12 text-right">
                                        <div class="btn btn--primary modal-close" v-on:click="vote(-10000, active_comment)">
                                            <span class="btn__text">
                                                {{ __('lang.BUTTON.REPORT_COMMENT') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<div v-pre class="modal-instance">
    <div id="modal-download" data-modal-id="modal-download" class="modal-container">
        <div class="modal-content section-modal">
            <section class="unpad ">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-md-8 col-sm-12 m-0-auto">
                            <div class="feature feature-1">
                                <div class="feature__body boxed boxed--lg boxed--border">
                                    <div class="modal-close modal-close-cross" v-on:click="cancelPay"></div>
                                    <div class="text-block">
                                        <h3>{{ __('lang.PUBLICATION.MODAL_DOWNLOAD_TITLE') }}</h3>
                                        <p>@{{ modal.alreadyPayed ? lang.PUBLICATION.MODAL_DOWNLOAD_TEXT_PAYED : lang.PUBLICATION.MODAL_DOWNLOAD_TEXT }}</p>
                                    </div>
                                    <form>
                                        <div v-if="!modal.alreadyPayed" class="row">
                                            <div class="col-md-12">
                                                <p class="text-p-form">{{ __('lang.MODAL.WALLET_AMOUNT') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-icon input-icon--right">
                                                    <i class="">@{{ modal.symbol }}</i>
                                                    <input disabled v-model="modal.amount" type="number" step="0.001" name="input" />
                                                    <p class="amount-save mt-4" >{{ __('lang.WALLET.BALANCE') }}: @{{ modal.balance }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col text-right">
                                                <div v-if="modal.confirmed" class="btn btn--sm modal-close"
                                                     v-on:click="cancelPay">
                                                    <span class="btn__text text__dark">{{ __('lang.BUTTON.CANCEL') }}</span>
                                                </div>
                                                <div v-if="modal.alreadyPayed" v-on:click="confirmDownload" class="btn btn--sm btn--primary modal-close" >
                                                    <span class="btn__text">{{ __('lang.BUTTON.DOWNLOAD') }}</span>
                                                </div>
                                                <div v-else v-on:click="confirmDownload" class="btn btn--sm btn--primary" >
                                                    <span class="btn__text">@{{ modal.confirmed ? lang.BUTTON.PAY : lang.BUTTON.CONFIRM }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<div v-pre class="modal-instance">
    <div id="modal-promote" data-modal-id="modal-promote" class="modal-container">
        <div class="modal-content section-modal">
            <section class="unpad ">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-md-8 col-sm-12">
                            <div class="feature feature-1">
                                <div class="feature__body boxed boxed--lg boxed--border">
                                    <div class="modal-close modal-close-cross"></div>
                                    <div class="text-block">
                                        <h3>{{ __('lang.PUBLICATION.PROMOTE_TITLE') }}</h3>
                                        <p>{{ __('lang.PUBLICATION.PROMOTE_TEXT') }}</p>
                                    </div>
                                    <form>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="text-p-form">{{ __('lang.MODAL.WALLET_AMOUNT') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-icon input-icon--right">
                                                    <i class="">CBD ($)</i>
                                                    <input v-model="amount" type="number" step="0.001" name="input" />
                                                    <p class="amount-save mt-4" >{{ __('lang.WALLET.BALANCE') }}: @{{ user.cbd_balance }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col text-right">
                                                <div v-on:click="makePromotion" class="btn btn--sm btn--primary" >
                                                    <span class="btn__text">{{ __('lang.BUTTON.PROMOTE') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<div v-pre class="modal-instance">
    <div id="modal-delete" data-modal-id="modal-delete" class="modal-container modal-report">
        <div class="modal-content">
            <section class="unpad ">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="boxed boxed--lg bg--white feature">
                                <div class="modal-close modal-close-cross"></div>
                                <h3>{{ __('lang.PUBLICATION.MODAL_DELETE_TITLE') }}</h3>
                                <div class="feature__body">
                                    <p class="mb-0">{{ __('lang.PUBLICATION.MODAL_DELETE_BODY1') }}</p>
                                    <p class="mb-0">{{ __('lang.PUBLICATION.MODAL_DELETE_BODY2') }}</p>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12 text-right">
                                        <div class="btn btn--sm modal-close">
                                            <span class="btn__text text__dark">{{ __('lang.BUTTON.CANCEL') }}</span>
                                        </div>
                                        <div v-on:click="deletePublication" class="btn btn--sm btn--primary" >
                                            <span class="btn__text">{{ __('lang.BUTTON.CONFIRM') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
