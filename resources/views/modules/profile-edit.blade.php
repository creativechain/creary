<div class="col-12 col-lg-10 offset-lg-1">
    <div class="w-100">
        <h4 class="title-section-profile">{{ __('lang.EDIT_PROFILE.SECTION_TITLE') }}</h4>
    </div>
    <div class="w-100">
        <div id="profile-edit" class="boxed boxed--sm boxed--border">
            <form action="">

                <div class="row mt-3">
                    <div class="col-12 col-md-6">
                        <label>{{ __('lang.EDIT_PROFILE.PUBLIC_NAME') }}</label>
                        <input class="validate-required" type="text"
                            v-bind:maxlength="CONSTANTS.TEXT_MAX_SIZE.PROFILE.PUBLIC_NAME" v-model="profile.publicName"
                            placeholder="{{ __('lang.EDIT_PROFILE.INPUT_PUBLIC_NAME') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label>{{ __('lang.EDIT_PROFILE.ABOUT') }}</label>
                        <input class="validate-required" type="textarea"
                            v-bind:maxlength="CONSTANTS.TEXT_MAX_SIZE.PROFILE.ABOUT" v-model="profile.about"
                            placeholder="{{ __('lang.EDIT_PROFILE.INPUT_ABOUT') }}" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h4 class="mt-2 add-link-title">Add Links</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="input-select">
                            <select>
                                <option value="-1" selected>Select website</option>
                                <template v-for="(s, i) in editableSocials">
                                    <template v-if="s">
                                        <option v-bind:value="i" v-on:click="addSocial(i)">@{{ s.name }}</option>
                                    </template>
                                </template>
<!--                                <option value="0">Website</option>
                                <option value="2">Twitter</option>
                                <option value="3">Instagram</option>
                                <option value="4">Youtube</option>
                                <option value="5">Vimeo</option>
                                <option value="6">Linkt</option>
                                <option value="7">Opensea</option>
                                <option value="8">KnownOrigin</option>
                                <option value="9">SuperRare</option>
                                <option value="10">Raribe</option>
                                <option value="11">Makersplace</option>
                                <option value="12">Foundation</option>
                                <option value="13">Async Art</option>
                                <option value="14">Hice et nunc</option>-->
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <!-- repeat -->
                        <div class="row pr-2">
                            <template v-for="(s, i) in profile.other.socials">
                                <div v-if="s" class="col-12 social-input mb-3">
                                    <div class="header-web-link">
                                        <span><i v-bind:class="'icon-' + s.name.toLowerCase().replaceAll(' ', '')"></i> @{{ s.name }}</span>
                                        <a href="#" v-on:click="deleteSocial($event, i)">{{ __('lang.COMMON.DELETE') }}</a>
                                    </div>
                                    <label class="sr-only" for="inlineFormInputGroup"></label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">@{{ s.baseUrl }}</div>
                                        </div>
                                        <input v-model="profile.other.socials[i].profile" type="text" class="form-control" id="inlineFormInputGroup" placeholder="">
                                    </div>
                                </div>
                            </template>

<!--                            <div class="col-12 social-input mb-3">
                                <div class="header-web-link">
                                    <span><i class="icon-twitter"></i> Twitter</span>
                                    <a href="#">delete</a>
                                </div>
                                <label class="sr-only" for="inlineFormInputGroup"></label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">twitter.com/</div>
                                    </div>
                                    <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="">
                                </div>
                            </div>
                            <div class="col-12 social-input mb-3">
                                <div class="header-web-link">
                                    <span><i class="icon-twitter"></i> Instaglam</span>
                                    <a href="#">delete</a>
                                </div>
                                <label class="sr-only" for="inlineFormInputGroup"></label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">instagram.com/</div>
                                    </div>
                                    <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="">
                                </div>
                            </div>-->
                        </div>
                    </div>

                </div>
                <div class="row">
                        <div class="col-12 col-md-6">
                            <label>{{ __('lang.EDIT_PROFILE.LANGUAGE') }}</label>
                            <div class="input-select">
                                <select v-model="profile.lang">
                                    <option v-for="l in isoLangs" v-bind:value="l.code">@{{ l.nativeName }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label>{{ __('lang.EDIT_PROFILE.ADULT_CONTENT') }}</label>
                            <div class="input-select">
                                <select v-model="profile.adult_content">
                                    <option disabled value="">{{ __('lang.EDIT_PROFILE.SELECT_OPTION') }}</option>
                                    <option value="hide">{{ __('lang.PROFILE_SETTINGS.ADULT_HIDE') }}</option>
                                    <option value="warn">{{ __('lang.PROFILE_SETTINGS.ADULT_WARN') }}</option>
                                    <option value="show">{{ __('lang.PROFILE_SETTINGS.ADULT_SHOW') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label>{{ __('lang.EDIT_PROFILE.POST_REWARDS') }}</label>
                            <div class="input-select">
                                <select v-model="profile.post_rewards">
                                    <option disabled value="">{{ __('lang.EDIT_PROFILE.SELECT_OPTION') }}</option>
                                    <option value="0">{{ __('lang.PROFILE_SETTINGS.REWARDS_DECLINE') }}</option>
                                    <option value="50">{{ __('lang.PROFILE_SETTINGS.REWARDS_50_50') }}</option>
                                    <option value="100">{{ __('lang.PROFILE_SETTINGS.REWARDS_100') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label>{{ __('lang.EDIT_PROFILE.COMMENT_REWARDS') }}</label>
                            <div class="input-select">
                                <select v-model="profile.comment_rewards">
                                    <option disabled value="">{{ __('lang.EDIT_PROFILE.SELECT_OPTION') }}</option>
                                    <option value="0">{{ __('lang.PROFILE_SETTINGS.REWARDS_DECLINE') }}</option>
                                    <option value="50">{{ __('lang.PROFILE_SETTINGS.REWARDS_50_50') }}</option>
                                    <option value="100">{{ __('lang.PROFILE_SETTINGS.REWARDS_100') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="btn btn--sm btn--primary" v-on:click="sendAccountUpdate">
                                <span class="btn__text font-weight-bold">
                                    {{ __('lang.BUTTON.SAVE') }}
                                </span>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>
