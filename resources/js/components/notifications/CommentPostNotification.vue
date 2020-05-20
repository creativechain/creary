<template>
    <div class="row row-list-user">
        <div class="col-md-9">
            <div class="row-list-user-display">
                <div v-if="commenter != null" class="user-avatar">
                    <a v-bind:href="'/@' + commenter.name">
                        <Avatar v-bind:account="commenter"></Avatar>
                    </a>
                </div>
                <div class="list-data-user">
                    <p v-if="commenter != null" style="display: block ruby">
                        <Username class="name color--link" v-bind:inline="1" v-bind:user="commenter.name" v-bind:name="commenter.metadata.publicName"></Username>
                        <span>{{ moment(data.created_at, 'YYYY-MM-DD HH:mm:ss').fromNow() }}</span>
                    </p>
                    <p v-if="commenter && discussion">
                        <img src="/img/icons/notifications/icon_comment_noti.svg" alt="" class="icon-notification-list" />
                        <span v-html="text"></span>
                        <!--<span>{{ data.body }}</span>-->
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div v-if="discussion" class="row-img-notification">
                <a v-bind:href="'/@' + discussion.link">
                    <div class="img-user-notification" v-lazy:background-image="discussion.metadata.featuredImage.url"></div>
                </a>
            </div>
        </div>
    </div>
</template>

<script>

    import Avatar from "../Avatar";
    import Username from "../Username";
    import {getAccounts, getDiscussion} from "../../common/common";

    export default {
        components: {
            Avatar, Username
        },
        props: {
            session: {
                type: [Object, Boolean]
            },
            user: {
                type: [Object, Boolean]
            },
            data: Object
        },
        computed: {
            text: function () {
                let t = this.reply ? this.lang.NOTIFICATIONS.USER_COMMENTED_YOUR_COMMENT : this.lang.NOTIFICATIONS.USER_COMMENTED_YOUR_POST;
                if (this.commenter.metadata && this.commenter.metadata.publicName) {
                    return String.format(t, this.commenter.metadata.publicName);
                } else {
                    return String.format(t, this.commenter.name);
                }
            }
        },
        data: function () {
            return {
                commenter: null,
                discussion: null,
                ready: false,
                reply: false,
                lang: window.lang,
                moment: window.moment,
            }
        },
        mounted() {
            let that = this;
            let calls = 2;
            let onReady = function() {
                calls--;
                if (calls < 0) {
                    that.ready = true;
                    that.$forceUpdate();
                }
            };

            getAccounts([this.data.author], (err, accounts) => {
                if (!err) {
                    that.commenter = accounts[0];

                    onReady();
                }

            });

            let onDiscussion = function (err, discussion) {
                if (!err) {

                    //Comment of comment
                    if (discussion.parent_author && discussion.parent_permlink) {
                        that.reply = true;
                        return getDiscussion(discussion.parent_author,  discussion.parent_permlink, onDiscussion);
                    }

                    that.discussion = discussion;

                    onReady();
                }
            };

            getDiscussion(this.data.parent_author, this.data.parent_permlink, onDiscussion)
        },
        methods: {
            onFollow: function () {

            }
        }
    }
</script>
