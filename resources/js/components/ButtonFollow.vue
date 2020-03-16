<template>
    <div v-on:click="performFollow" v-on:mouseleave="onleave" v-on:mouseover="onover" class="btn btn-sm running ld ld-ext-right font-weight-bold" v-bind:class="btnClasses">
        <div class="btn__text" v-bind:class="textClasses"></div>
        {{ text }}
        <div v-if="state === states.FOLLOWING_OP || state === states.UNFOLLOWING_OP" class="loading-button">
            <div class="">
                <svg viewBox="0 0 50 50" class="spinner">
                    <circle cx="25" cy="25" r="22.5" class="ring"></circle>
                    <circle cx="25" cy="25" r="22.5" class="line"></circle>
                </svg>
            </div>
        </div>
    </div>
</template>

<script>
    import { FOLLOW_STATE } from './states';
    import { requireRoleKey } from '../common/common';
    import { jsonstring } from "../lib/util";
    import Errors from '../lib/error';

    export default {
        props: {
            session: {
                type: [Object, Boolean]
            },
            account: {
                type: [Object, Boolean]
            },
            user: {
                type: String
            }
        },
        watch: {
            user: {
                immediate: true,
                deep: true,
                handler: function handler(newVal, oldVal) {
                    this.$forceUpdate();
                }
            },
            account: {
                immediate: true,
                deep: true,
                handler: function handler(newVal, oldVal) {
                    this.$forceUpdate();
                }
            }
        },
        computed: {
            btnClasses: function btnClasses() {
                return {
                    'btn--primary': this.state === this.states.NO_FOLLOWING || this.state === this.states.UNFOLLOWED || this.state === this.states.FOLLOWING_OP,
                    'btn-following': !this.over && (this.state === this.states.FOLLOWING || this.state === this.states.FOLLOWED) || this.state === this.states.UNFOLLOWING_OP,
                    'btn-unfollow': this.over && (this.state === this.states.FOLLOWING || this.state === this.states.FOLLOWED)
                };
            },
            textClasses: function textClasses() {
                return {
                    'text__dark': !this.over && (this.state === this.states.FOLLOWING || this.state === this.states.FOLLOWED),
                    'ld-ring': this.state === this.states.FOLLOWING_OP || this.state === this.states.UNFOLLOWING_OP,
                    'ld-ring-blue': this.state === this.states.UNFOLLOWING_OP
                };
            }
        },
        data: function data() {
            return {
                lang: lang,
                over: false,
                states: FOLLOW_STATE,
                state: FOLLOW_STATE.NO_FOLLOWING,
                lastState: FOLLOW_STATE.NO_FOLLOWING,
                text: null
            };
        },
        methods: {
            isStateOp: function isStateOp() {
                return this.state === this.states.FOLLOWING_OP || this.state === this.states.UNFOLLOWING_OP;
            },
            isStateFollowing: function isStateFollowing(state) {
                return state === this.states.FOLLOWING || state === this.states.FOLLOWED;
            },
            performFollow: function performFollow() {
                if (!this.isStateOp()) {
                    let operation = 'follow';
                    let that = this;
                    let session = this.$props.session;
                    let lastState = this.state;
                    this.state = this.isStateFollowing(this.state) ? this.states.UNFOLLOWING_OP : this.states.FOLLOWING_OP;

                    if (session) {
                        let followJson = {
                            follower: session.account.username,
                            following: this.$props.user,
                            what: this.isStateFollowing(lastState) ? [] : ['blog']
                        };
                        followJson = [operation, followJson];
                        requireRoleKey(session.account.username, 'posting', function (postingKey) {
                            crea.broadcast.customJson(postingKey, [], [session.account.username], operation, jsonstring(followJson), function (err, result) {
                                if (err) {
                                    that.state = lastState;
                                    that.$emit('follow', err);
                                } else {
                                    that.state = that.isStateFollowing(lastState) ? that.states.UNFOLLOWED : that.states.FOLLOWED;
                                    that.$emit('follow', null, result);
                                }
                            });
                        });
                    } else {
                        this.state = lastState;
                        this.$emit('follow', Errors.USER_NOT_LOGGED);
                    }
                }
            },
            onover: function onover() {
                this.over = true;
            },
            onleave: function onleave() {
                this.over = false;
            },
            isFollowing: function isFollowing() {
                return this.session && this.account.followings.includes(this.user);
            },
            updateText: function updateText() {
                switch (this.state) {
                    case this.states.NO_FOLLOWING:
                    case this.states.UNFOLLOWED:
                        this.text = this.lang.BUTTON.FOLLOW;
                        break;

                    case this.states.FOLLOWING:
                    case this.states.FOLLOWED:
                        this.text = this.over ? this.lang.BUTTON.UNFOLLOW : this.lang.BUTTON.FOLLOWING;
                        break;

                    case this.states.FOLLOWING_OP:
                        this.text = this.text = this.lang.BUTTON.FOLLOW;
                        break;

                    case this.states.UNFOLLOWING_OP:
                        this.text = this.lang.BUTTON.FOLLOWING;
                }
            }
        },
        updated: function updated() {
            if (!this.isStateOp()) {
                this.state = this.isFollowing() ? this.states.FOLLOWING : this.states.NO_FOLLOWING;
            }

            this.updateText();
        },
        mounted: function mounted() {
            this.state = this.isFollowing() ? this.states.FOLLOWING : this.states.NO_FOLLOWING;
            this.updateText();
        }
    }
</script>
