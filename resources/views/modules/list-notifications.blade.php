<div class="w-100">
    <div class="row">
        <div class="col-6">
            <h3 class="title-section-profile">{{ __('lang.NOTIFICATIONS.TITLE') }}</h3>
        </div>
        <div v-if="notifications.unread" class="col-6 text-right">
            <div v-on:click="markReadNotifications" class="btn btn--black">
                {{ __('lang.NOTIFICATIONS.BUTTON_MARK_READ') }}
            </div>
        </div>
    </div>
    <div class="boxed boxed--border row-list">
        <template v-for="n in notifications.all">
            @include('modules.notification-row')
        </template>
    </div>
</div>






