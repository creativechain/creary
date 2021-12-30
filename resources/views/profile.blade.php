@extends('layouts.app')

@section('content')
<div id="profile-container">
    <div  v-cloak class="new-profile">
        <div v-if="editMode" class="background-banner"></div>
        <div class="box-banner">
            <div v-if="editMode" class="edit-icon-banner" v-on:click="loadBanner"></div>
            <div v-if="editMode" class="delete-icon-banner" v-on:click="deleteBanner"></div>
            <input id="profile-edit-input-banner" class="hidden" type="file" accept="image/*" v-on:change="onLoadBanner">
        </div>

        <!--        <div class="banner-user" style="background-color: #EAEAEA"></div>-->
        <template v-if="editMode">
            <user-banner  v-bind:metadata="profile" ></user-banner>
        </template>
        <template v-else>
            <user-banner v-bind:metadata="state.user.metadata" ></user-banner>
        </template>
        <!-- edit profili -->
        <div class="container date-profile">
            <div class="row">
                <div class="col-12">
                    <div class="box-user-avatar">
                        <!-- change image profile -->
                        <div v-if="editMode" class="box-change-img-profile">
                            <div class="change-img-profile" v-on:click="loadAvatar"></div>
                            <div class="background-profile"></div>
                            <input id="profile-edit-input-avatar" class="hidden" type="file" accept="image/*" v-on:change="onLoadAvatar">
                        </div>

                        <!-- ojo el background-image NO ES REAL --->
                        <template v-if="editMode">
                            <edit-avatar v-bind:account="state.user" v-bind:metadata="profile"></edit-avatar>
                        </template>
                        <template v-else>
                            <avatar  v-bind:profile="true" v-bind:account="state.user"></avatar>
                        </template>

                        <!--                    <div class="img-user-avatar-profile"
                                                v-bind:style=" { 'background-image': url('https://ipfs.creary.net/ipfs/QmUhcxJVysx733PJgHx6RKowApjMsoG8cqC92CWvZiiaXP');">
                                            </div>-->
                    </div>
                </div>
            </div>
            <div class="row row-follow">
                <div v-if="session" class="col-12 text-right">
                    <a v-if="state.user.name !== account.user.name" href="" class="mr-3 bloquear">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="Grupo_9765"
                             data-name="Grupo 9765" wi   dth="25.112" height="21.656" viewBox="0 0 25.112 21.656">
                            <defs>
                                <clipPath id="clip-path">
                                    <path id="Trazado_1237" data-name="Trazado 1237" d="M0-44.64H25.112v21.656H0Z"
                                          transform="translate(0 44.64)" fill="#cacaca" />
                                </clipPath>
                            </defs>
                            <g id="Grupo_1997" data-name="Grupo 1997" clip-path="url(#clip-path)">
                                <g id="Grupo_1995" data-name="Grupo 1995" transform="translate(9.149 6.431)">
                                    <path id="Trazado_1235" data-name="Trazado 1235"
                                          d="M-16.774-4.425a17.629,17.629,0,0,0-4.7-4.169l-1.715,1.715a15.365,15.365,0,0,1,3.773,2.955A14.222,14.222,0,0,1-26.141.193a5.084,5.084,0,0,0,2.085-4.116,5.022,5.022,0,0,0-.317-1.794l-8.206,8.206a14.246,14.246,0,0,0,3.43.4c6.913,0,11.135-4.749,12.375-6.332a.831.831,0,0,0,0-.976"
                                          transform="translate(32.579 8.594)" fill="#cacaca" />
                                </g>
                                <g id="Grupo_1996" data-name="Grupo 1996" transform="translate(0 0)">
                                    <path id="Trazado_1236" data-name="Trazado 1236"
                                          d="M-3.045-11.782A14.224,14.224,0,0,1,3.683-15.9,5.082,5.082,0,0,0,1.6-11.782a5.219,5.219,0,0,0,.449,2.111l-1,1a14.513,14.513,0,0,1-4.09-3.113m19.6-10.765a1.2,1.2,0,0,0-1.663,0l-4.459,4.459a14.4,14.4,0,0,0-3.8-.5C-.275-18.59-4.5-13.84-5.736-12.257a.794.794,0,0,0,0,1A17.015,17.015,0,0,0-.75-6.928L-4.443-3.234a1.2,1.2,0,0,0,0,1.663,1.139,1.139,0,0,0,.817.342,1.1,1.1,0,0,0,.818-.342L16.506-20.885a1.109,1.109,0,0,0,.053-1.663"
                                          transform="translate(5.915 22.884)" fill="#cacaca" />
                                </g>
                            </g>
                        </svg>
                    </a>
                    <btn-follow v-if="state.user.name !== account.user.name && !state.user.buzz.blocked"
                                v-on:follow="onFollow" v-bind:session="session"
                                v-bind:account="account.user" v-bind:followuser="state.user.name" >
                    </btn-follow>

                    <a v-else-if="state.user.name === account.user.name && !editMode" class="btn btn--sm" v-bind:href="'/@' + session.account.username + '/settings'" v-on:click="navigateTo($event, 'settings')">
                        <span class="btn__text text__dark font-weight-bold">{{ __('lang.BUTTON.EDIT_PROFILE') }}</span>
                    </a>
                    <a v-else-if="state.user.name === account.user.name && editMode" class="btn btn--sm" v-bind:href="'/@' + session.account.username + '/projects'" v-on:click="navigateTo($event, 'projects')">
                        <span class="btn__text text__dark font-weight-bold">{{ __('lang.BUTTON.CANCEL_EDIT') }}</span>
                    </a>

                    <!--                <div class="btn btn&#45;&#45;primary">
                                        <span class="btn__text">
                                            Follow
                                        </span>
                                    </div>-->
                </div>
            </div>
            <div class="row row-content">
                <div class="col-12 col-md-6 col-xl-3">
                    <h1 class="user-name text-truncate">@{{ state.user.metadata.publicName || state.user.name }}</h1>
                    <p class="user text-truncate">@{{ '@' + state.user.name  }}</p>
                    <p>@{{ state.user.metadata.about }}</p>
                </div>
                <div class="col-12 col-md-6 col-xl-2 offset-xl-1">
                    <p class="cursor-link" v-on:click="navigateTo($event, 'followers')">{{ __('lang.PROFILE.FOLLOWERS') }}: <strong>@{{ state.user.follower_count }}</strong></p>
                    <p class="cursor-link" v-on:click="navigateTo($event, 'following')">{{ __('lang.PROFILE.FOLLOWING') }}: <strong>@{{ state.user.following_count }}</strong></p>
                    <p>{{ __('lang.PROFILE.REPUTATION') }}: <strong>@{{ state.user.buzz.level_title }} <span>(@{{ state.user.buzz.formatted }})</span></strong></p>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <p class="title-progress">CREA Energy:
                        <amount v-bind:value="getCGYBalance()" style="color: #222222; font-weight: bold; margin-left: 5px"></amount>
<!--                        <strong class="ml-2">
                            3894.<span class="sat">033</span>
                            CREA
                        </strong> -->
                        <img class="ml-2" style="width: 15px;" src="https://design.creary.net/img/wallet/creary-cgy-logo.svg" alt="">
                    </p>
                    <div class="energy">
                        <p class="title-progress">{{ __('lang.PROFILE.VOTING_ENERGY') }} <span class="ml-2"> @{{ state.user.voting_energy_percent }}%</span></p>
                        <div class="progress flow">
                            <span class="progress-bar" v-bind:style="{ width: state.user.voting_energy_percent + '%'}"></span>
                        </div>
                    </div>
                    <div class="energy">
                        <p class="title-progress">FLOW <span class="ml-2">@{{ state.user.voting_flowbar.flow_percent }}%</span></p>
                        <div class="progress flow">
                            <span class="progress-bar flow-bar" v-bind:style="{ width: state.user.voting_flowbar.flow_percent + '%' }"></span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="social">

                        <template v-for="social in profile.other.socials">
                            <a v-if="social" v-bind:href="social.link" class="a-social" data-toggle="tooltip" data-placement="top" v-bind:title="social.name" target="_blank">
                                <span v-bind:class="'icon-' + social.name.toLowerCase().replaceAll(' ', '')"></span>
                            </a>
                        </template>
<!--                        <a href="" class="a-social" data-toggle="tooltip" data-placement="top" title="Web"><span
                                class="icon-web"></span></a>
                        <a href="" class="a-social" data-toggle="tooltip" data-placement="top" title="Twitter"><span
                                class="icon-twitter"></span></a>
                        <a href="" class="a-social" data-toggle="tooltip" data-placement="top" title="Instagram"><span
                                class="icon-instagram"></span></a>
                        <a href="" class="a-social" data-toggle="tooltip" data-placement="top" title="Youtube"><span
                                class="icon-youtube"></span></a>
                        <a href="" class="a-social" data-toggle="tooltip" data-placement="top" title="Vimeo"><span
                                class="icon-vimeo"></span></a>
                        <a href="" class="a-social" data-toggle="tooltip" data-placement="top" title="LinkT"><span
                                class="icon-linkt"></span></a>
                        <a href="" class="a-social" data-toggle="tooltip" data-placement="top" title="Superrare"><span
                                class="icon-superrare"></span></a>
                        <a href="" class="a-social" data-toggle="tooltip" data-placement="top" title="Rarible"><span
                                class="icon-rarible"></span></a>
                        <a href="" class="a-social" data-toggle="tooltip" data-placement="top" title="Makersplace"><span
                                class="icon-makersplace"></span></a>
                        <a href="" class="a-social" data-toggle="tooltip" data-placement="top" title="Knownorigin"><span
                                class="icon-knownorigin"></span></a>-->
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div  class="">
        <div v-cloak>
            <section v-cloak class="cta cta-4 space--xxs border--bottom navbar-filter-profile  bg-white">
                <div class="container">
                    <div class="row">
                        @include('modules.navbar-profile')
                    </div>
                </div>
            </section>

            <section v-cloak class="bg--secondary pt-5">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <section class="space--sm unpad--top">
                                <div class="container">
                                    <div v-show="navbar.section === 'projects' && session && session.account.username === state.user.name && Object.keys(state.content).length === 0"
                                         class="row welcome-profile-empty">
                                        @include('modules.welcome-profile-empty')
                                    </div>
                                    <div
                                        v-show="(!session || session.account.username != state.user.name) && Object.keys(state.content).length === 0 && navbar.section === 'projects'">
                                        <h3>{{ __('lang.PROFILE.NO_POSTS_PROFILE') }}</h3>
                                    </div>
                                    <div v-show="navbar.section === 'projects'" class="row project-profile">
                                        <template v-if="state.user.profile_blocked">
                                            <h3 class="error-color-form font-weight-bold">
                                                {{ __('lang.PUBLICATION.BLOCKED_USER_ALERT_TITLE') }}
                                            </h3>
                                            <p>
                                                {{ __('lang.PUBLICATION.BLOCKED_PROFILE_ALERT_MESSAGE') }}
                                            </p>
                                        </template>
                                        <template v-else-if="state.user.buzz.blocked">
                                            <h3 class="error-color-form font-weight-bold">
                                                {{ __('lang.PUBLICATION.BLOCKED_USER_ALERT_TITLE') }}
                                            </h3>
                                            <p>
                                                {{ __('lang.PUBLICATION.BLOCKED_USER_ALERT_MESSAGE') }}
                                            </p>
                                        </template>
                                        <template v-else>
                                            <template v-for="p in state.discussion_idx[''].profile">
                                                @include('modules.post-view-card')
                                            </template>
                                        </template>


                                    </div>
                                    <div v-show="session && account.user.name === state.user.name && navbar.section === 'notifications'"
                                         class="row view-notifications order-0 order-md-1 view-wallet">
                                        @include('modules.list-notifications')
                                    </div>
                                    <div v-show="navbar.section === 'author-rewards'">
                                        <div v-pre>
                                            <div v-cloak id="profile-author-rewards" class="row view-rewards">
                                                @include('modules.view-rewards-author')
                                            </div>
                                        </div>
                                    </div>
                                    <div v-show="navbar.section === 'curation-rewards'" class="view-rewards">
                                        <div v-pre>
                                            <div v-cloak id="profile-curation-rewards" class="row view-rewards">
                                                @include('modules.view-rewards-curation')
                                            </div>
                                        </div>
                                    </div>
                                    <div v-show="session && account.user.name === state.user.name && navbar.section === 'blocked'"
                                         class="view-notifications">
                                        <div v-pre>
                                            @include('modules.list-blocked')
                                        </div>

                                    </div>
                                    <div v-show="navbar.section === 'wallet'" class="row view-wallet">
                                        @include('modules.view-wallet')
                                    </div>
                                    <div v-show="navbar.section === 'settings'" class="row view-edit-profile">
                                        @include('modules.profile-edit')
                                    </div>
                                    <div v-show="navbar.section === 'followers'" class="view-notifications">
                                        <div v-pre>
                                            @include('modules.list-followers')
                                        </div>
                                    </div>
                                    <div v-show="navbar.section === 'following'" class="view-notifications">
                                        <div v-pre>
                                            @include('modules.list-following')
                                        </div>

                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        @include('modules.post-view-navigation')

    </div>
</div>

@include('layouts.modals')

<script src="{{ asset('js/control/profile.js') }}"></script>
<script src="{{ asset('js/control/post-navigation.js') }}"></script>

@endsection
