<template>
    <div class="row row-list-user">
        <div class="col-9 col-md-9">
            <div class="row-list-user-display">
                <div v-if="mentioner != null" class="user-avatar">
                    <a v-bind:href="'/@' + mentioner.name">
                        <Avatar v-bind:account="mentioner"></Avatar>
                    </a>
                </div>
                <div class="list-data-user">
                    <p v-if="mentioner != null" class="row-user-date" style="display: block ruby">
                        <Username class="name color--link" v-bind:inline="1" v-bind:user="mentioner.name" v-bind:name="mentioner.metadata.publicName"></Username>
                        <span>{{ toLocaleDate(data.created_at).fromNow() }}</span>
                    </p>
                    <p v-if="mentioner && discussion">
                        <img src="/img/icons/notifications/icon_mention_noti.svg" alt="" class="icon-notification-list" />
                        <span v-html="text"></span>
                        <!--<span>{{ data.body }}</span>-->
                    </p>
                </div>
            </div>
        </div>
        <div class="col-3 col-md-3">
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
    import {toLocaleDate} from "../../lib/util";

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
                let t = this.lang.NOTIFICATIONS.USER_MENTIONED_YOU ;
                if (this.mentioner.metadata && this.mentioner.metadata.publicName) {
                    return String.format(t, this.mentioner.metadata.publicName);
                } else {
                    return String.format(t, this.mentioner.name);
                }
            }
        },
        data: function () {
            return {
                mentioner: null,
                discussion: null,
                ready: false,
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
                    that.mentioner = accounts[0];

                    onReady();
                }

            });

            getDiscussion(this.data.parent_author, this.data.parent_permlink, (err, discussion) => {
                if (!err) {
                    //Comment of comment
                    if (discussion.parent_author && discussion.parent_permlink) {
                        return getDiscussion(discussion.parent_author,  discussion.parent_permlink, this);
                    }

                    that.discussion = discussion;

                    onReady();
                }
            })
        },
        methods: {
            toLocaleDate: toLocaleDate,
            onFollow: function () {

            }
        }
    }
</script>
