<template>
    <div class="row row-list-user">
        <div class="col-md-9">
            <div class="row-list-user-display">
                <div v-if="voter != null" class="user-avatar">
                    <a v-bind:href="'/@' + voter.name">
                        <Avatar v-bind:account="voter"></Avatar>
                    </a>
                </div>
                <div class="list-data-user">
                    <p v-if="voter != null" style="display: block ruby">
                        <Username class="name color--link" v-bind:inline="1" v-bind:user="voter.name" v-bind:name="voter.metadata.publicName"></Username>
                        <span>{{ toLocaleDate(data.created_at).fromNow() }}</span>
                    </p>
                    <p v-if="voter && discussion">
                        <img src="/img/icons/notifications/icon_like_noti.svg" alt="" class="icon-notification-list" />
                        <span v-html="text"></span>
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
    import { Asset } from "../../lib/amount";
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
                let voteValue = Asset.parseString(`${this.data.vote_value} CBD`);
                let t = this.lang.NOTIFICATIONS.USER_LIKES_YOUR_POST ;
                if (this.voter.metadata && this.voter.metadata.publicName) {
                    return String.format(t, this.voter.metadata.publicName, voteValue.toPlainString());
                } else {
                    return String.format(t, this.voter.name, voteValue.toPlainString());
                }
            }
        },
        data: function () {
            return {
                voter: null,
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

            getAccounts([this.data.voter], (err, accounts) => {
                if (!err) {
                    that.voter = accounts[0];

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
            toLocaleDate: toLocaleDate,
            onFollow: function () {

            }
        }
    }
</script>
