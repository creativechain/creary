<template>
    <div class="row row-list-user">
        <div class="col-9 col-md-9">
            <div class="row-list-user-display">
                <div v-if="downloader != null" class="user-avatar">
                    <a v-bind:href="'/@' + downloader.name">
                        <Avatar v-bind:account="downloader"></Avatar>
                    </a>
                </div>
                <div class="list-data-user">
                    <p v-if="downloader != null" class="row-user-date" style="display: block ruby">
                        <Username class="name color--link" v-bind:inline="1" v-bind:user="downloader.name" v-bind:name="downloader.metadata.publicName"></Username>
                        <span>{{ moment(data.created_at, 'YYYY-MM-DD HH:mm:ss').fromNow() }}</span>
                    </p>
                    <p v-if="downloader && discussion">
                        <img src="/img/icons/notifications/icon_download_noti.svg" alt="" class="icon-notification-list" />
                        <span v-html="text"></span>
                        <!--<span>{{ data.body }}</span>-->
                    </p>
                </div>
            </div>
        </div>
        <div class="col-3 col-md-3">
            <div v-if="discussion" class="row-img-notification">
                <a v-bind:href="discussion.permlink">
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
                let t = this.lang.NOTIFICATIONS.USER_DOWNLOADED_YOUR_CONTENT ;
                if (this.downloader.metadata && this.downloader.metadata.publicName) {
                    return String.format(t, this.downloader.metadata.publicName);
                } else {
                    return String.format(t, this.downloader.name);
                }
            }
        },
        data: function () {
            return {
                downloader: null,
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

            getAccounts([this.data.downloader], (err, accounts) => {
                if (!err) {
                    that.downloader = accounts[0];

                    onReady();
                }

            });

            getDiscussion(this.data.comment_author, this.data.comment_permlink, (err, discussion) => {
                if (!err) {
                    that.discussion = discussion;

                    onReady();
                }
            })
        },
        methods: {
            onFollow: function () {

            }
        }
    }
</script>
