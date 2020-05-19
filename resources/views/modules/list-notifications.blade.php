<div class="col-md-12">
    <h3 class="title-section-profile">{{ __('lang.NOTIFICATIONS.TITLE') }}</h3>
    <div v-on:click="markReadNotifications" class="btn btn-primary active">
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
