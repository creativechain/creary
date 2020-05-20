<template>
    <div class="row row-list-user">
        <div class="col-md-9">
            <div class="row-list-user-display">
                <div v-if="recommender != null" class="user-avatar">
                    <a v-bind:href="'/@' + recommender.name">
                        <Avatar v-bind:account="recommender"></Avatar>
                    </a>
                </div>
                <div class="list-data-user">
                    <p v-if="recommender != null" style="display: block ruby">
                        <Username class="name color--link" v-bind:inline="1" v-bind:user="recommender.name" v-bind:name="recommender.metadata.publicName"></Username>
                        <span>{{ moment(data.created_at, 'YYYY-MM-DD HH:mm:ss').fromNow() }}</span>
                    </p>
                    <p v-if="recommender && discussion">
                        <img src="/img/icons/notifications/icon_recommend_noti.svg" alt="" class="icon-notification-list" />
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
                let t = this.lang.NOTIFICATIONS.USER_RECOMMENDED_YOUR_POST ;
                if (this.recommender.metadata && this.recommender.metadata.publicName) {
                    return String.format(t, this.recommender.metadata.publicName);
                } else {
                    return String.format(t, this.recommender.name);
                }
            }
        },
        data: function () {
            return {
                recommender: null,
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

            getAccounts([this.data.account], (err, accounts) => {
                if (!err) {
                    that.recommender = accounts[0];

                    onReady();
                }

            });

            getDiscussion(this.data.author, this.data.permlink, (err, discussion) => {
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
