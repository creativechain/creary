<template>
    <div v-on:mouseover="onOver(true)" v-on:mouseleave="onOver(false)" class="circle-like-post bs-popover-left"
         v-bind:class="circleClasses" role="button" data-toggle="popover" data-trigger="hover" data-placement="left"
         data-html="true" v-bind:title="tooltipTitle" v-bind:data-original-title="tooltipTitle" v-bind:data-content="payouts">
        <div class="lds-heart size-20 size-30-like post-like" v-bind:class="likeClasses" v-on:click="makeVote">
            <div></div>
        </div>
    </div>
</template>

<script>
    import { LIKE_STATE } from './states';
    import { requireRoleKey } from '../common/common';
    import { toLocaleDate } from '../lib/util';
    import R from '../lib/resources';

    export default {
        props: {
            payouts: [String, Boolean],
            session: [Object, Boolean],
            post: {
                type: Object
            }
        },
        watch: {
            post: {
                immediate: true,
                deep: true,
                handler: function handler(newVal, oldVal) {
                    this.state = this.hasVote() ? this.states.LIKED : this.states.NO_LIKE;
                }
            }
        },
        computed: {
            circleClasses: function circleClasses(){
                return {
                    'circle-like-post-active': (!this.over && this.state === this.states.LIKED) || (this.over && (this.state === this.states.NO_LIKE || this.state === this.states.NO_LIKE_END))
                }
            },

            likeClasses: function likeClasses() {
                return {
                    'like-normal': !this.over && (this.state === this.states.NO_LIKE || this.state === this.states.NO_LIKE_END) || (this.over && (this.state === this.states.LIKED || this.state === this.states.LIKED_END)),
                    'like-normal-activate': !this.over && (this.state === this.states.LIKED || this.state === this.states.LIKED_END) || (this.over && (this.state === this.states.NO_LIKE || this.state === this.states.NO_LIKE_END)),
                    'active-like': this.state === this.states.LIKE_OP
                };
            },
            tooltipTitle: function() {
                return this.post.up_votes.length + ' Likes';
            }
        },
        data: function data() {
            return {
                R: R,
                states: LIKE_STATE,
                state: 0,
                over: false
            };
        },
        methods: {
            onOver: function onOver(isOver) {
                this.over = isOver;
            },
            hasPaid: function hasPaid() {
                let now = moment();
                let payout = toLocaleDate(this.$props.post.cashout_time);
                return now.isAfter(payout);
            },
            getVote: function getVote() {
                let session = this.$props.session;
                let post = this.$props.post;

                if (session && post) {
                    let upVotes = post.up_votes;

                    for (let x = 0; x < upVotes.length; x++) {
                        let vote = upVotes[x];

                        if (session.account.username === vote.voter) {
                            return vote;
                        }
                    }
                }

                return null;
            },
            removeVote: function removeVote(username) {
                let post = this.post;

                if (post) {
                    let upVotes = post.up_votes;
                    let i = -1;

                    for (let x = 0; x < upVotes.length; x++) {
                        let vote = upVotes[x];

                        if (username === vote.voter) {
                            i = x;
                            break;
                        }
                    }

                    if (i > -1) {
                        this.post.up_votes.slice(i, 1);
                        this.$forceUpdate();
                    }
                }
            },
            hasVote: function hasVote() {
                let v = this.getVote();
                return v != null;
            },
            makeVote: function makeVote(event) {
                if (event) {
                    event.preventDefault();
                }

                if (this.state !== this.states.LIKE_OP) {
                    let that = this;
                    let session = this.$props.session;
                    let post = this.$props.post;
                    let username = session ? session.account.username : null;
                    let percent = that.hasVote() ? 0 : 10000;

                    requireRoleKey(username, 'posting', function (postingKey, username) {
                        that.state = that.states.LIKE_OP;
                        crea.broadcast.vote(postingKey, username, post.author, post.permlink, percent, function (err, result) {
                            if (err) {
                                that.$emit('vote', err, null, post);
                            } else {
                                if (percent > 0) {
                                    that.post.up_votes.push({
                                        voter: username,
                                        author: post.author,
                                        permlink: post.permlink,
                                        percent: percent
                                    });
                                    that.state = that.states.LIKED_END;
                                } else {
                                    that.removeVote(username);
                                    that.state = that.states.NO_LIKE_END;
                                }
                                that.$emit('vote', null, result, post);

                                //Update tooltip;
                                let circleLike = $('.circle-like-post');
                                let realTooltip = circleLike.attr('aria-describedby');
                                console.log('realTooltip', realTooltip);
                                realTooltip = $('#' + realTooltip);
                                if (realTooltip.length > 0) {
                                    $('.popover-header').html(that.tooltipTitle)
                                }
                            }
                        });
                    });
                }
            }
        },
        mounted: function mounted() {
            this.state = this.hasVote() ? LIKE_STATE.LIKED : LIKE_STATE.NO_LIKE;
        },
        updated: function updated() {
            if (this.state !== LIKE_STATE.LIKE_OP) {
                this.state = this.hasVote() ? LIKE_STATE.LIKED : LIKE_STATE.NO_LIKE;
            }
        }
    }
</script>
