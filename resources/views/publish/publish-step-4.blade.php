<div class="step-4">
    <div class="boxed boxed--border">
        <div class="section-title-step">
            <h4 class="title-steps">{{ __('lang.PUBLISH.LICENSE_TITLE') }}</h4>
            <span class="description-step-title">{{ __('lang.PUBLISH.LICENSE_SUBTITLE') }}</span>
        </div>
        <form action="" class="row">
            <div class="col-md-12">
                <label>{{ __('lang.PUBLISH.LICENSE_PUBLIC_DOMAIN') }}</label>
                <div class="input-radio-step-2">
                    <div class="input-radio">
                        <input id="radio-1a-1" type="radio" v-bind:value="LICENSE.FREE_CONTENT.flag"
                               v-bind:disabled="noLicense !== LICENSE.NO_LICENSE.flag"
                               v-model="publicDomain"/>
                        <label for="radio-1a-1" ></label>
                        <span class="input__label">{{ __('lang.COMMON.YES') }}</span>
                    </div>
                    <div class="input-radio">
                        <input id="radio-1a-2" type="radio" v-bind:value="LICENSE.NO_LICENSE.flag"
                               v-bind:disabled="noLicense !== LICENSE.NO_LICENSE.flag"
                               v-model="publicDomain" />
                        <label for="radio-1a-2" ></label>
                        <span class="input__label">{{ __('lang.COMMON.NO') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <label>{{ __('lang.PUBLISH.LICENSE_ADAPTATIONS') }}</label>
                <div class="input-radio-step-2">
                    <div class="input-radio">
                        <input id="radio-1-1" type="radio" name="share" v-model="share"
                               v-bind:value="LICENSE.NO_LICENSE.flag"
                               v-bind:disabled="noLicense !== LICENSE.NO_LICENSE.flag || publicDomain !== LICENSE.NO_LICENSE.flag" />
                        <label for="radio-1-1"></label>
                        <span class="input__label">{{ __('lang.COMMON.YES') }}</span>
                    </div>
                    <div class="input-radio">
                        <input id="radio-1-2" type="radio" name="share" v-model="share"
                               v-bind:value="LICENSE.NON_DERIVATES.flag"
                               v-bind:disabled="noLicense !== LICENSE.NO_LICENSE.flag || publicDomain !== LICENSE.NO_LICENSE.flag"/>
                        <label for="radio-1-2"></label>
                        <span class="input__label">{{ __('lang.COMMON.NO') }}</span>
                    </div>
                    <div class="input-radio">
                        <input id="radio-1-3" type="radio" name="share" v-model="share"
                               v-bind:value="LICENSE.SHARE_ALIKE.flag"
                               v-bind:disabled="noLicense !== LICENSE.NO_LICENSE.flag || publicDomain !== LICENSE.NO_LICENSE.flag" />
                        <label for="radio-1-3"></label>
                        <span class="input__label">{{ __('lang.PUBLISH.LICENSE_SHARE') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <label>{{ __('lang.PUBLISH.LICENSE_COMMERCIAL') }}</label>
                <div class="input-radio-step-2">
                    <div class="input-radio">
                        <input id="radio-2-1" type="radio" name="commercial" v-model="commercial"
                               v-bind:value="LICENSE.NO_LICENSE.flag"
                               v-bind:disabled="noLicense !== LICENSE.NO_LICENSE.flag || publicDomain !== LICENSE.NO_LICENSE.flag" />
                        <label for="radio-2-1"></label>
                        <span class="input__label">{{ __('lang.COMMON.YES') }}</span>
                    </div>
                    <div class="input-radio">
                        <input id="radio-2-2" type="radio" name="commercial" v-model="commercial"
                               v-bind:value="LICENSE.NON_COMMERCIAL.flag"
                               v-bind:disabled="noLicense !== LICENSE.NO_LICENSE.flag || publicDomain !== LICENSE.NO_LICENSE.flag" />
                        <label for="radio-2-2"></label>
                        <span class="input__label">{{ __('lang.COMMON.NO') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <label>{{ __('lang.PUBLISH.LICENSE_NO_LICENSE') }}</label>
                <div class="input-radio-step-2">
                    <div class="input-radio">
                        <input id="radio-3-1" type="radio" name="nolicense"
                               v-model="noLicense"
                               v-bind:value="LICENSE.NON_PERMISSION.flag" />
                        <label for="radio-3-1"></label>
                        <span class="input__label">{{ __('lang.COMMON.YES') }}</span>
                    </div>
                    <div class="input-radio">
                        <input id="radio-3-2" type="radio" name="nolicense"
                               v-model="noLicense"
                               v-bind:value="LICENSE.NO_LICENSE.flag" />
                        <label for="radio-3-2"></label>
                        <span class="input__label">{{ __('lang.COMMON.NO') }}</span>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="boxed boxed--border">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <ul class="list-unstyled list-inline ul-icon-license">
                            <template v-for="i in getLicense().getIcons()">
                                <li><img v-lazy="i" alt="" /></li>
                            </template>
                        </ul>
                    </div>
                    <div class="col-md-12 text-center row-text-license">
                        <p>@{{ getLicense().toString() }}</p>
                        <span v-if="!getLicense().has(LICENSE.NON_PERMISSION.flag)">@{{ getLicense().toLocaleString() }} 4.0 {{ __('lang.LICENSE.INTERNATIONAL') }} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 offset-md-2 text-center row-text-finish">
            <p>{{ __('lang.PUBLISH.LICENSE_TEXT_PUBLISH') }}</p>
            <span>{{ __('lang.PUBLISH.LICENSE_TEXT_INMUTABLE') }}</span>
        </div>
    </div>
</div>

