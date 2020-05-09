{{--<div class="row row-list-user">
    <div class="col-md-9">
        <div class="row-list-user-display">
            <div class="user-avatar">
                <div class="img-user-avatar"></div>
            </div>
            <div class="list-data-user">
                <p>Comando C <span>4 days ago</span></p>
                <p>
                    <img src="../img/icons/comments.svg" alt="" class="icon-notification-list" />
                    <span>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="row-img-notification">
            <div class="img-user-notification" style="background-image: url('/img/crea-web/publish/demo-vertical.jpg'); "></div>
        </div>
    </div>
</div>

<div class="row row-list-user">
    <div class="col-md-9">
        <div class="row-list-user-display">
            <div class="user-avatar">
                <div class="img-user-avatar"></div>
            </div>
            <div class="list-data-user">
                <p>Comando C <span>4 days ago</span></p>
                <p><img src="../img/icons/like_ACT_RED.svg" alt="" class="icon-notification-list" /><span>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod</span></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="row-img-notification">
            <div class="img-user-notification" style="background-image: url('/img/crea-web/publish/demo-vertical.jpg'); "></div>
        </div>
    </div>
</div>

<div class="row row-list-user align-items-center">
    <div class="col-md-9">
        <div class="row-list-user-display">
            <div class="user-avatar">
                <div class="img-user-avatar"></div>
            </div>
            <div class="list-data-user">
                <p>Comando C <span>4 days ago</span></p>
                <p><img src="../img/icons/like_ACT_RED.svg" alt="" class="icon-notification-list" /><span>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod</span></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 text-right">
        <div class="align-self-center">
            <a href="/publish.php" class="btn btn--sm btn--primary">
                <span class="btn__text">Follow</span>
            </a>
        </div>
    </div>
</div>--}}

<follow-notification v-if="n.type === 'follow'"
                     v-bind:session="session"
                     v-bind:user="account.user"
                     v-bind:data="n">

</follow-notification>

<like-post-notification v-if="n.type === 'vote'"
                      v-bind:session="session"
                      v-bind:user="account.user"
                      v-bind:data="n">

</like-post-notification>

<comment-post-notification v-if="n.type === 'comment'"
                        v-bind:session="session"
                        v-bind:user="account.user"
                        v-bind:data="n">

</comment-post-notification>

<recommend-post-notification v-if="n.type === 'reblog'"
                           v-bind:session="session"
                           v-bind:user="account.user"
                           v-bind:data="n">

</recommend-post-notification>

<download-notification v-if="n.type === 'comment_download'"
                             v-bind:session="session"
                             v-bind:user="account.user"
                             v-bind:data="n">

</download-notification>
