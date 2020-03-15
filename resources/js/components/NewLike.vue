<template>
    <div class="row-likes">
        <div class="d-flex w-100">
            <div>
                <div class="text-right">
                    <div ref="heart_like" class="lds-heart size-20 post-like" v-bind:class="likeClasses" v-on:click="makeVote">
                        <div></div>
                    </div>
                    <div class="dropdown inline post-like-count">
                        <span class="dropdown__trigger"> {{ post.up_votes.length }}</span>
                        <div class="dropdown__container">
                            <div class="container">
                                <div class="row">
                                    <div class="col-4 col-sm-3 col-md-6 col-lg-2 dropdown__content amount-post-view-home">
                                        <ul class="list-inline text-right">
                                            <template v-for="v in (post.up_votes.length > 10 ? 10 : post.up_votes.length)">
                                                <li class="list-inline-item w-100">
                                                    <a v-if="(v-1) < 10" v-bind:href="'/@' + post.up_votes[v-1].voter" class="text-truncate">+{{ post.up_votes[v-1].voter }}</a>
                                                    <span v-else class="text-truncate" >+{{ "..and " + post.up_votes.length - 10  + " users"}}</span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { LIKE_STATE } from './states';
    import { moment, requireRoleKey } from '../common/common';
    import { toLocaleDate } from '../lib/util';
    import R from '../lib/resources';
    import { crea } from '../common/conf';

    export default {
        props: {
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
        data: function data() {
            return {
                R: R,
                states: LIKE_STATE,
                state: 0
            };
        },
        computed: {
            likeClasses: function likeClasses() {
                return {
                    'like-normal': this.state === this.states.NO_LIKE || this.state === this.states.NO_LIKE_END,
                    'like-normal-activate': this.state === this.states.LIKED || this.state === this.states.LIKED_END,
                    'active-like': this.state === this.states.LIKE_OP
                };
            }
        },
        methods: {
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

                    console.log(i, username);
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

                    console.log(this.hasVote(), percent);
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
                            }

                            console.log(err, that.state, that.$refs.heart_like);

                            //Clear focus for heart-like. Needed in Smartphone view
                            that.$refs.heart_like.blur();
                            that.$forceUpdate();

                        });
                    });
                }
            }
        },
        mounted: function mounted() {
            this.state = this.hasVote() ? LIKE_STATE.LIKED : LIKE_STATE.NO_LIKE;
        },
        updated: function updated() {
            console.log('Updating card like', this.state, this.hasVote());
            if (this.state !== LIKE_STATE.LIKE_OP) {
                this.state = this.hasVote() ? LIKE_STATE.LIKED : LIKE_STATE.NO_LIKE;
            }
        }
    }
</script>
