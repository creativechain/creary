<div class="col-6">
    <h3 class="title-section-profile">{{ __('lang.NOTIFICATIONS.TITLE') }}</h3>
</div>
<div class="col-6 text-right">
    <div v-on:click="markReadNotifications" class="btn btn--black">
        {{ __('lang.NOTIFICATIONS.BUTTON_MARK_READ') }}
    </div>
</div>
<div class="col-md-12">
    <div class="boxed boxed--border row-list">
        <template v-for="n in notifications">
            @include('modules.notification-row')
        </template>
    </div>
</div>
