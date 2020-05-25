<div class="col-md-12 text-center menu-secondary-profile">
    <ul class="list-inline">
        <li class="list-inline-item">
            <a v-bind:href="'/@' + state.user.name + '/projects'" v-on:click="navigateTo($event, 'projects')" v-bind:class="{ active: navbar.section === 'projects' }">
                {{ __('lang.PROFILE.SECONDARY_MENU_PROJECTS') }}
            </a>
        </li>

        <li class="dropdown list-inline-item">
            <span class="dropdown__trigger">{{ __('lang.PROFILE.SECONDARY_MENU_REWARDS') }}</span>
            <div class="dropdown__container">
                <div class="container">
                    <div class="row">
                        <div class="dropdown__content col-lg-2 col-md-4 col-sm-12 text-left">
                            <ul class="menu-vertical ul-rewards">
                                <li class="">
                                    <a v-bind:href="'/@' + state.user.name + '/author-rewards'" v-on:click="navigateTo($event, 'author-rewards')">
                                        {{ __('lang.REWARDS.SECONDARY_MENU_AUTHOR') }}
                                    </a>
                                </li>
                                <li class="">
                                    <a v-bind:href="'/@' + state.user.name + '/curation-rewards'" v-on:click="navigateTo($event, 'curation-rewards')">
                                        {{ __('lang.REWARDS.SECONDARY_MENU_CURATION') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </li>

        <li v-if="isUserProfile()" class="list-inline-item">
            <a v-bind:href="'/@' + state.user.name + '/notifications'" v-on:click="navigateTo($event, 'notifications')" v-bind:class="{ active: navbar.section === 'notifications' }">
                {{ __('lang.PROFILE.SECONDARY_MENU_NOTIFICATIONS') }}
            </a>
        </li>

        <li v-if="isUserProfile()" class="list-inline-item">
            <a v-bind:href="'/@' + state.user.name + '/blocked'" v-on:click="navigateTo($event, 'blocked')" v-bind:class="{ active: navbar.section === 'blocked' }">
                {{ __('lang.PROFILE.SECONDARY_MENU_BLOCKED') }}
            </a>
        </li>

        <li class="list-inline-item">
            <a v-bind:href="'/@' + state.user.name + '/wallet'" v-on:click="navigateTo($event, 'wallet')" v-bind:class="{ active: navbar.section === 'wallet' }">
                {{ __('lang.PROFILE.SECONDARY_MENU_WALLET') }}
            </a>
        </li>
    </ul>
</div>
