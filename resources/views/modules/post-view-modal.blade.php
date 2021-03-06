<div id="post-navigation-view">
    <div class="closed-modal modal-close modal-close-cross">
        <svg xmlns="http://www.w3.org/2000/svg" width="13.999" height="14" viewBox="0 0 13.999 14">
            <path id="ignore_X" data-name="ignore X" d="M12,10.586,6.414,5,5,6.414,10.585,12,5,17.586,6.414,19,12,13.414,17.586,19,19,17.586,13.414,12,19,6.414,17.586,5Z" transform="translate(-5 -5)" fill="#cacaca" fill-rule="evenodd"/>
        </svg>
    </div>
    <div class="box-next-post" style="z-index: 1">
        <div v-if="state.postIndex > 0" class="next-left">
            <div class="cursor-link" v-on:click="lastPost">
                <img v-lazy="'{{ asset('img/next_post/esperra.svg') }}'" alt="">
            </div>
        </div>
        <div v-if="state.postIndex <= (state.discussions.length - 2)" class="next-right">
            <div class="cursor-link" v-on:click="nextPost">
                <img v-lazy="'{{ asset('img/next_post/dreta.svg') }}'" alt="">
            </div>
        </div>
    </div>
    <div class="modal-content">
        <div v-cloak v-if="state.post">
            @include('modules.post')
        </div>
    </div>

    <div v-if="state.post && navigation && !state.author.buzz.blocked" class="position-circle-fixed">
        <post-like-big v-on:vote="onVote"
                       v-bind:session="session"
                       v-bind:post="state.post"
                       v-bind:payouts="getPendingPayouts(state.post)">

        </post-like-big>
    </div>
</div>

