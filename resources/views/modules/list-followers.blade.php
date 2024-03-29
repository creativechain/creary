<div v-cloak id="follower-container" class="row">
    <div class="w-100">
        <h3 class="title-section-profile">Followers</h3>
    </div>


    <div v-if="Object.keys(follower).length === 0" class="col-md-12 bg--light text-center row-empty-notifications">
        <img v-lazy="'{{ asset('/img/empty.svg') }}'" alt="" class="img-empty"/>
        <div class="row mt-2">
            <div class="col-md-12">
                <p class="title mb-0">{{ __('lang.HOME.EMPTY_TITLE') }}</p>
                <p class="subtitle">{{ __('lang.HOME.EMPTY_SUBTITLE') }}</p>
            </div>
        </div>
    </div>
    <div class="w-100">
        <div class="boxed boxed--border row-list pt-0 pb-0">
            <div v-for="f in follower" class="row row-list-user">
                <div class="col-md-9">
                    <div class="row-list-user-display">
                        <div class="user-avatar">
                            <a v-bind:href="'/@' + f.name ">
                                <avatar v-bind:account="f"></avatar>
                            </a>
                        </div>
                        <div class="list-data-user">
                            <username v-bind:user="f.name" v-bind:name="f.metadata.publicName"></username>
                            <p><span>@{{ f.metadata.about || "-" }}</span></p>
                        </div>
                    </div>
                </div>
                <div v-if="session && account.user.name !== f.name" class="col-md-3 align-self-center text-right">
                    <btn-follow v-on:follow="onFollow" v-bind:session="session"
                                v-bind:account="account.user"
                                v-bind:followuser="f.name" >
                    </btn-follow>
                </div>
            </div>
        </div>
    </div>
</div>

