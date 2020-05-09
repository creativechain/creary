<div class="col-md-12">
    <h3 class="title-section-profile">Notifications</h3>
</div>
<div class="col-md-12">
    <div class="boxed boxed--border row-list">

        <template v-for="n in notifications">
            @include('modules.notification-row')
        </template>


    </div>
</div>
