<div v-cloak id="blocked-container" class="row">
    <div class="w-100">
        <h3 class="title-section-profile">{{ __('lang.PROFILE.TITLE_BLOCKED') }}</h3>
    </div>
    <div class="w-100">
        <div class="boxed boxed--border row-list pt-0 pb-0">
            <div v-for="b in blocked" class="row row-list-user">
                <div class="col-md-9">
                    <div class="row-list-user-display">
                        <div class="user-avatar">
                            <a v-bind:href="'/@' + b.name ">
                                <avatar v-bind:account="b"></avatar>
                            </a>
                        </div>
                        <div class="list-data-user">
                            <username v-bind:user="b.name" v-bind:name="b.metadata.publicName"></username>
                            <p><span>@{{ b.metadata.about || "-"  }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 align-self-center text-right">
                    <div class="btn btn--sm btn--primary" v-on:click="unlock(b.name)">
                        <span class="btn__text font-weight-bold">{{ __('lang.BUTTON.UNLOCK') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



