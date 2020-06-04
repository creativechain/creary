<div class="w-100 padding-b-10" v-bind:class="{ hidden: !(session && state.user.name === session.account.username && hasRewardBalance()) }">
    <div class="alert bg--primary">
        <div class="alert__body">
            <span>{{ __('lang.WALLET.PENDING_REWARDS') }}:  @{{ state.user.reward_crea_balance }},
                @{{ state.user.reward_cbd_balance }} {{ __('lang.COMMON.AND') }}
                @{{ getCGYReward() }}
            </span>
            <span class="row-liquid">
                <a href="#" class="btn btn--sm btn--secondary" v-on:click="claimRewards">
                    <span class="btn__text color-secondary font-weight-bold">{{ __('lang.BUTTON.CLAIM_REWARDS') }}</span>
                </a>
            </span>
        </div>
    </div>
</div>

<div class="w-100">
    <div class="row">
        <div class="col-6">
            <h3 class="title-section-profile">{{ __('lang.NOTIFICATIONS.TITLE') }}</h3>
        </div>
        {{--<div v-if="notifications.unread" class="col-6 text-right">
            <div v-on:click="markReadNotifications" class="btn btn--black">
                {{ __('lang.NOTIFICATIONS.BUTTON_MARK_READ') }}
            </div>
        </div>--}}
    </div>
    <div v-if="notifications.all.length > 0" class="boxed boxed--border row-list">
        <template v-for="n in notifications.all">
            @include('modules.notification-row')
        </template>
    </div>
    <div v-else>
        {{ __('lang.NOTIFICATIONS.NO_NOTIFICATIONS') }}
    </div>
</div>
