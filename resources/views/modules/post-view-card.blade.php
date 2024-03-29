<div class="col-xl-2-cust col-xl-3-cust col-lg-4-cust col-sm-6 col-md-4 col-lg-3 col-xl-2 masonry__item" v-bind:class="{ 'row-simple-view': simpleView }">
    <div class="card card-2 card-home">

        <div class="square-card">
            <div class="content-card">
                <div class="card__top">
                    <a v-bind:href="state.content[p].url" v-on:click="openPost(state.content[p], $event)">
                        <div v-if="!state.accounts[state.content[p].author].buzz.blocked" class="img-post-list"
                             v-lazy:background-image="getFeaturedImage(state.content[p]).url">

                        </div>
                        <div v-else class="img-post-list" v-lazy:background-image="'{{ asset('img/crea-web/image-block-creary.jpg') }}'">

                        </div>
                    </a>

                    <recommend v-if="session" v-bind:feed="isFeed()" v-bind:user="account.user" v-bind:session="session" v-bind:post="state.content[p]"></recommend>
                </div>
            </div>
        </div>


        <div class="card__body">
            <h4 v-on:click="openPost(state.content[p])">@{{ state.content[p].title }}</h4>
            <!--<p class="description-post-box text-truncate" v-html="getTags(state.content[p])"></p>-->
            <ul class="list-inline list-unstyled w-100">

                <!-- maquetacion like fichas home -->
                <li v-if="!state.accounts[state.content[p].author].buzz.blocked" class="ul-social li-like-home list-inline-item pos-relative">
                    <new-like v-on:vote="onVote" v-bind:session="session" v-bind:post="state.content[p]"></new-like>
                </li>

                <li>
                    <div class="dropdown dropdown-price">
                        <span class="dropdown__trigger"> @{{ getPayout(state.content[p]) }}
                            <i class="stack-down-open"></i>
                        </span>
                        <div class="dropdown__container price">
                            <div class="container">
                                <div class="row">
                                    <div v-if="state.content[p].refused_payouts" class="col-md-12 dropdown__content amount-post-view-home">
                                        <p class="error-color-form">{{ __('lang.HOME.DROPDOWN_REFUSED_PAYOUT') }} <span class="refused-payout">@{{ getPayout(state.content[p]) }}</span></p>
                                        <p v-if="hasPromotion(state.content[p])">{{ __('lang.HOME.PROMOTION_COST') }} : @{{ getPromotion(state.content[p]) }}</p>
                                    </div>
                                    <div v-else class="col-md-12 dropdown__content amount-post-view-home">
                                        <p class="title">@{{ hasPaid(state.content[p]) ? lang.HOME.DROPDOWN_PAST_PAYOUT : lang.HOME.DROPDOWN_PENDING_PAYOUT }} @{{ getPayout(state.content[p]) }}</p>
                                        <p v-if="!hasPaid(state.content[p])">
                                            @{{ getPendingPayouts(state.content[p]) }}
                                        </p>
                                        <p>@{{ getPayoutPostDate(state.content[p]) }}</p>
                                        <p v-if="hasPromotion(state.content[p])">{{ __('lang.HOME.PROMOTION_COST') }} : @{{ getPromotion(state.content[p]) }}</p>
                                    </div>
                                </div><!--end row-->
                            </div><!--end container-->
                        </div><!--end dropdown container-->
                    </div>
                </li>
                <li class="float-right li-comment">
                    <p>
                        <img v-lazy="'{{ asset('img/crea-web/comments.svg') }}'" alt="" />
                        <span>@{{ state.content[p].children }}</span>
                    </p>
                </li>
            </ul>
        </div>

        <div class="card__bottom card-show">
            <ul class="list-inline list-unstyled w-100">
                <li class="list-inline-item">
                    <div class="dropdown dropdown-autor">
                        <div class="row-flex">
                            <div class="user-avatar size-25-avatar">
                                <avatar class="size-25-avatar" v-bind:account="state.accounts[state.content[p].author]"></avatar>
                            </div>
                            <span class="dropdown-autor-span-name text-truncate">@{{ state.accounts[state.content[p].author].metadata.publicName || state.content[p].author }}</span>
                        </div>
                        <div class="dropdown__container dropdown-info-user">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12 col-sm-3 col-md-12 dropdown__content">
                                        <div class="row">
                                            <div class="col text-center">
                                                <div class="user-avatar">
                                                    <a v-bind:href="'/@' + state.content[p].author">
                                                        <avatar v-bind:account="state.accounts[state.content[p].author]"></avatar>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-center">
                                                <p class="name text-truncate">
                                                    <linkname v-bind:user="state.content[p].author" v-bind:name="state.accounts[state.content[p].author].metadata.publicName"></linkname>
                                                </p>
                                                <p class="user  text-truncate">
                                                    <linkname v-bind:user="state.content[p].author"></linkname>
                                                </p>
                                                <p class="description-user text-truncate">@{{ state.accounts[state.content[p].author].metadata.about || "" }}</p>
                                                <p class="email-user text-truncate">
                                                    @{{ state.accounts[state.content[p].author].metadata.contact || state.accounts[state.content[p].author].metadata.web || "" }}
                                                </p>
                                            </div>
                                        </div>
                                        <div v-if="session && state.content[p].author !== session.account.username && !state.accounts[state.content[p].author].buzz.blocked" class="row">
                                            <div class="col text-center m-btn-follow">
                                                <btn-follow class="button-follow" v-on:follow="onFollow"
                                                            v-bind:session="session" v-bind:account="account.user"
                                                            v-bind:followuser="state.content[p].author">
                                                </btn-follow>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <p class="title-stats">{{ __('lang.HOME.DROPDOWN_USER_PROFILE_PROJECTS') }}</p>
                                                <span>@{{ state.accounts[state.content[p].author].post_count }}</span>
                                            </div>
                                            <div class="col">
                                                <p class="title-stats">{{ __('lang.HOME.DROPDOWN_USER_PROFILE_FOLLOWERS') }}</p>
                                                <span>@{{ state.accounts[state.content[p].author].follower_count }}</span>
                                            </div>
                                            <div class="col">
                                                <p class="title-stats">{{ __('lang.HOME.DROPDOWN_USER_PROFILE_FOLLOWING') }}</p>
                                                <span>@{{ state.accounts[state.content[p].author].following_count }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--end row-->
                            </div><!--end container-->
                        </div><!--end dropdown container-->
                    </div>
                </li>

                <li class="list-inline-item float-right li-buzz">
                    <div class="dropdown cursor-link">
                        <div class="img-buzz" v-bind:class="getBuzzClass(state.accounts[state.content[p].author])"></div>
                        <div class="dropdown__container dropdown-info-user">
                            <div class="container">
                                <div class="row">
                                    <div class="col-7  col-sm-2 col-md-6 dropdown__content">
                                        <div class="row">
                                            <div class="col text-center">
                                                <span>
                                                    @{{ state.accounts[state.content[p].author].buzz.level_title }} (@{{ state.accounts[state.content[p].author].buzz.formatted }})
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--end row-->
                            </div><!--end container-->
                        </div><!--end dropdown container-->
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
