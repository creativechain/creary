@extends('layouts.app')

@section('content')

    <div class="main-container view-profile">
        <div id="profile-container" v-cloak>
            <section v-cloak class="cta cta-4 space--xxs border--bottom navbar-filter">
                <div class="container">
                    <div class="row">
                        @include('modules.navbar-profile')
                    </div>
                </div>
            </section>

            <section v-cloak class="bg--secondary p-top-15">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-xl-3">
                            @include('modules.profile-info')
                        </div>
                        <div class="col-lg-9 col-xl-9">
                            <section class="space--sm unpad--top">
                                <div class="container">
                                    <div v-show="navbar.section === 'projects' && session && session.account.username === state.user.name && Object.keys(state.content).length === 0" class="row welcome-profile-empty">
                                        @include('modules.welcome-profile-empty')
                                    </div>
                                    <div v-show="(!session || session.account.username != state.user.name) && Object.keys(state.content).length === 0 && navbar.section === 'projects'">
                                        <h3>{{ __('lang.PROFILE.NO_POSTS_PROFILE') }}</h3>
                                    </div>
                                    <div v-show="navbar.section === 'projects'" class="row project-profile">
                                        <template v-if="!state.user.buzz.blocked">
                                            <template v-for="p in state.discussion_idx[''].profile">
                                                @include('modules.post-view-card')
                                            </template>
                                        </template>
                                        <template v-else>
                                            <h3 class="error-color-form font-weight-bold">
                                                {{ __('lang.PUBLICATION.BLOCKED_USER_ALERT_TITLE') }}
                                            </h3>
                                            <p>
                                                {{ __('lang.PUBLICATION.BLOCKED_USER_ALERT_MESSAGE') }}
                                            </p>
                                        </template>
                                    </div>
                                    <div v-show="session && account.user.name === state.user.name && navbar.section === 'notifications'" class="row view-notifications order-0 order-md-1 view-wallet" >
                                        @include('modules.list-notifications')
                                    </div>
                                    <div v-show="navbar.section === 'author-rewards'" >
                                        <div v-pre >
                                            <div v-cloak id="profile-author-rewards" class="row view-rewards">
                                                @include('modules.view-rewards-author')
                                            </div>
                                        </div>
                                    </div>
                                    <div v-show="navbar.section === 'curation-rewards'" class="view-rewards" >
                                        <div v-pre >
                                            <div v-cloak id="profile-curation-rewards" class="row view-rewards">
                                                @include('modules.view-rewards-curation')
                                            </div>
                                        </div>
                                    </div>
                                    <div v-show="session && account.user.name === state.user.name && navbar.section === 'blocked'" class="view-notifications" >
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
                                        <div v-pre >
                                            @include('modules.list-followers')
                                        </div>
                                    </div>
                                    <div v-show="navbar.section === 'following'" class="view-notifications">
                                        <div v-pre >
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
        <script src="{{ asset('js/control/post-navigation.js') }}"></script>
        <script src="{{ asset('js/control/profile.js') }}"></script>

        @include('layouts.modals')

    </div>

@endsection
