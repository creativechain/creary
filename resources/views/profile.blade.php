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
                    </div>
                </div>
            </div>
            <div class="row row-follow">
                <div v-if="session" class="col-12 text-right">
                    <a v-if="state.user.name !== account.user.name" href="" class="mr-3 bloquear">
                        <img src="{{ asset('img/profile/eye_block.svg') }}" alt="{{ __('lang.BUTTON.EDIT_PROFILE') }}">
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
                    <div class="title-progress">CREA Energy:
                        <amount v-bind:value="getCGYBalance()" style="color: #222222; font-weight: bold; margin-left: 5px"></amount>

                        <img class="ml-2" style="width: 15px;" src="{{ asset('img/wallet/creary-cgy-logo.svg') }}" alt="">
                    </div>
                    <div class="energy">
                        <p class="title-progress">
                            {{ __('lang.PROFILE.VOTING_ENERGY') }}
                            <span class="ml-2"> @{{ state.user.voting_energy_percent }}%</span>
                        </p>
                        <div class="progress flow">
                            <span class="progress-bar" v-bind:style="{ width: state.user.voting_energy_percent + '%'}"></span>
                        </div>
                    </div>
                    <div class="energy">
                        <p class="title-progress">
                            FLOW
                            <span class="ml-2">@{{ state.user.voting_flowbar.flow_percent }}%</span>
                        </p>
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
                                    <div v-show="(!session || session.account.username != state.user.name) && Object.keys(state.content).length === 0 && navbar.section === 'projects'">
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
    </div>

    @include('modules.post-view-navigation')
</div>

@include('layouts.modals')

<script src="{{ asset('js/control/profile.js') }}"></script>
<script src="{{ asset('js/control/post-navigation.js') }}"></script>

@endsection
