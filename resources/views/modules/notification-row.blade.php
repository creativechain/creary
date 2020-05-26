<follow-notification v-if="n.type === 'follow'"
                     v-bind:session="session"
                     v-bind:user="account.user"
                     v-bind:data="n"
                     v-on:follow="onFollow">

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

<mention-notification v-if="n.type === 'mention'"
                           v-bind:session="session"
                           v-bind:user="account.user"
                           v-bind:data="n">

</mention-notification>

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
