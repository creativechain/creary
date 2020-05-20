<template>
    <div class="row row-list-user align-items-center">
        <div class="col-md-9">
            <div class="row-list-user-display">
                <div v-if="follower != null" class="user-avatar">
                    <a v-bind:href="'/@' + follower.name">
                        <Avatar v-bind:account="follower"></Avatar>
                    </a>
                </div>
                <div class="list-data-user">
                    <p v-if="follower != null" style="display: block ruby">
                        <Username class="name color--link" v-bind:inline="1" v-bind:user="follower.name" v-bind:name="follower.metadata.publicName"></Username>
                        <span>{{ toLocaleDate(data.created_at).fromNow() }}</span>
                    </p>
                    <p v-if="follower">
                        <img src="/img/icons/notifications/icon_follow_noti.svg" alt="" class="icon-notification-list" />
                        <span>{{ text }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3 text-right">
            <div v-if="follower != null && following != null" class="align-self-center">
                <ButtonFollow v-on:follow="onFollow" v-bind:session="session"
                            v-bind:account="user"
                            v-bind:followuser="following.name" >

                </ButtonFollow>
            </div>
        </div>
    </div>
</template>

<script>
    import ButtonFollow from "../ButtonFollow";
    import Avatar from "../Avatar";
    import Username from "../Username";
    import {getAccounts} from "../../common/common";
    import {toLocaleDate} from "../../lib/util";

    export default {
        components: {
            ButtonFollow, Avatar, Username
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
                let t = this.data.what.length > 0 ? this.lang.NOTIFICATIONS.USER_FOLLOWING_YOU : this.lang.NOTIFICATIONS.USER_UNFOLLOWING_YOU;
                if (this.follower.metadata && this.follower.metadata.publicName) {
                    return String.format(t, this.follower.metadata.publicName);
                } else {
                    return String.format(t, this.follower.name);
                }
            }
        },
        data: function () {
            return {
                follower: null,
                following: null,
                ready: false,
                lang: window.lang,
                moment: window.moment,
            }
        },
        mounted() {
            let that = this;
            getAccounts([this.data.follower, this.data.following], (err, accounts) => {
                if (!err) {
                    for (let a of accounts) {
                        if (a.name === this.data.follower) {
                            that.follower = a;
                        } else if (a.name === this.data.following) {
                            that.following = a;
                        }
                    }

                    that.ready = true;
                    this.$forceUpdate();
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
