<div class="step-2">
    <div class="boxed boxed--border">
        <div class="section-title-step">
            <h4 class="title-steps">{{ __('lang.PUBLISH.INFO_TITLE') }}</h4>
            <span class="description-step-title">{{ __('lang.PUBLISH.INFO_SUBTITLE') }}</span>
        </div>
        <div class="boxed boxed--border box-image-step-2" v-on:click="loadFeaturedImage">
            <div class="">
                <div v-if="!featuredImage.url" class="col-md-4 offset-md-4 col-sm-12 text-center" style="position: relative;top: 60px;">
                    <img v-lazy="'{{ asset('img/crea-web/publish/b-img-portada.png') }}'" alt="" />
                    <p>{{ __('lang.PUBLISH.INFO_IMAGE') }}</p>
                    <p class="disabled">@{{ String.format(lang.PUBLISH.IMAGE_MAX_FILE_SIZE, humanFileSize(CONSTANTS.FILE_MAX_SIZE.POST_PREVIEW.IMAGE)) }}</p>
                </div>
                <div v-else class="upload-img">
                    <div class="delete-img-step-1">
                        <a href="#">X</a>
                    </div>
                    <img v-lazy="featuredImage.url" alt="" />

                </div>
            </div>
            <div class="pos-vertical-center text-center hidden">
                <div class="row">
                    <div v-if="!featuredImage.url" class="col-md-4 offset-4">
                        <div class="row row-options-steps-1">
                            <div class="col">
                                <img v-lazy="'{{ asset('img/crea-web/publish/b-img-portada.png') }}'" alt="" class="img-protada" />
                                <p>{{ __('lang.PUBLISH.INFO_IMAGE') }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="upload-img">
                        <div class="delete-img-step-1">
                            <a href="#">X</a>
                        </div>
                        <img v-lazy="featuredImage.url" alt="" />

                    </div>
                </div>
            </div>
            <input ref="publishInputCover" class="hidden" type="file" accept="image/*" v-on:change="onLoadFeaturedImage" />

        </div>

    </div>
    <div class="boxed boxed--border">
        <div class="section-title-step">
            <h4 class="title-steps">{{ __('lang.PUBLISH.PROJECT_INFO') }}</h4>
        </div>

        <form action="" class="row">
            <div class="col-md-12">
                <label>{{ __('lang.PUBLISH.INFO_POST_TITLE') }}</label>
                <input v-model="title" v-bind:maxlength="CONSTANTS.TEXT_MAX_SIZE.TITLE" v-on:input="removeTitleEmojis"
                       class="validate-required" type="text" name="My Input" placeholder="{{ __('lang.PUBLISH.INFO_INPUT_TITLE') }}" />
            </div>
            <div class="col-md-12">
                <label>{{ __('lang.PUBLISH.INFO_DESCRIPTION') }}</label>
                <input v-model="description" v-bind:maxlength="CONSTANTS.TEXT_MAX_SIZE.DESCRIPTION" v-on:input="removeDescriptionEmojis"
                       class="validate-required" type="text" name="My Input" placeholder="{{ __('lang.PUBLISH.INFO_INPUT_DESCRIPTION') }}" />
            </div>

            <div class="col-md-12">
                <label>{{ __('lang.PUBLISH.MAIN_CATEGORY') }}</label>
                <div class="input-select">
                    <select v-model="mainCategory">
                        <option value="">{{ __('lang.PUBLISH.SELECT_A_CATEGORY') }}</option>
                        <option v-for="c in selectableCategories" v-bind:value="c.tag">
                            @{{ c.text }}
                        </option>
                    </select>
                </div>

            </div>

            <div class="col-md-12">
                <label>{{ __('lang.PUBLISH.INFO_TAGS') }}</label>
                <input id="publish-tags" class="validate-required"
                       type="text" value="" placeholder="{{ __('lang.PUBLISH.INFO_INPUT_TAGS') }}" />
            </div>

            <div class="col-md-12">
                <label for="">{{ __('lang.PUBLISH.BENEFICIARIES') }}</label>
                <div class="d-flex mb-3">
                    <div class="mr-5" style="display: inline-flex; align-items: center;width: 15%;">
                        <input type="number" v-model="mainBeneficiary.weight" name="account" placeholder="0" disabled class="disabled text-center"/> <span style="margin-left: 5px;font-size: 16px;"> %</span>
                    </div>

                    <div class="input-icon">
                        <i class="material-icons email">alternate_email</i>
                        <input type="text" v-model="mainBeneficiary.account" name="weight" disabled class="disabled"/>
                    </div>
                </div>

                <template v-for="b in Object.keys(beneficiaries)">
                    <div v-if="b !== null" class="d-flex mb-3" >
                        <div class="mr-5" style="display: inline-flex; align-items: center;width: 15%;">
                            <input type="number"
                                   autocomplete="off"
                                   v-model="beneficiaries[b].weight"
                                   v-on:input="updateBeneficiariesWeight"
                                   v-bind:name="'weight' + b"
                                   placeholder="0" class="text-center"/> <span style="margin-left: 5px;font-size: 16px;"> %</span>
                        </div>

                        <div class="input-icon">
                            <i class="material-icons email">alternate_email</i>
                            <input type="text"
                                   autocomplete="off"
                                   v-model="beneficiaries[b].account"
                                   v-bind:name="'account' + b"
                                   placeholder="" />
                        </div>
                        <div class="input-icon cursor-link">
                            <a href="" v-on:click="deleteBeneficiary($event, b)" class="close"><i class="material-icons email">close</i></a>
                        </div>
                    </div>
                </template>


                <div class="btn btn--sm" v-on:click="addBeneficiary">
                    <span class="btn__text text__dark">{{ __('lang.PUBLISH.ADD_BENEFICIARY') }}</span>
                </div>
            </div>





            <div class="col-md-12">
                <label>{{ __('lang.PUBLISH.QUESTION') }}</label>
                <div class="input-radio-step-2">
                    <div class="input-radio">
                        <span class="input__label">{{ __('lang.COMMON.YES') }}</span>
                        <input id="radio-1" type="radio" name="adult" v-model="adult" v-bind:value="true" />
                        <label for="radio-1"></label>
                    </div>
                    <div class="input-radio">
                        <span class="input__label">{{ __('lang.COMMON.NO') }}</span>
                        <input id="radio-2" type="radio" name="adult" v-model="adult" v-bind:value="false"/>
                        <label for="radio-2"></label>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <p class="error-color-form">
                    @{{ error || "" }}
                </p>
            </div>
        </form>
    </div>
</div>

