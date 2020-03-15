<template>
    <div>
        <span class="d-flex">
            {{ index }}
            <div class="lds-heart size-20" v-bind:class="voteClasses" v-on:click="makeVote" style="margin-top: 5px;">
                <div></div>
            </div>
        </span>
    </div>
</template>

<script>
    import { WITNESS_STATE } from './states';
    import { requireRoleKey } from '../common/common';
    import R from '../lib/resources';
    import { crea } from '../common/conf';

    export default {
        props: {
            session: [Object, Boolean],
            account: [Object, Boolean],
            witness: Object,
            index: Number
        },
        watch: {
            account: {
                immediate: true,
                deep: true,
                handler: function handler(newVal, oldVal) {
                    this.state = this.hasVote() ? this.states.VOTED : this.states.NO_VOTE;
                }
            }
        },
        data: function data() {
            return {
                R: R,
                states: WITNESS_STATE,
                state: WITNESS_STATE.NO_VOTE
            };
        },
        computed: {
            voteClasses: function () {
                return {
                    'like-normal': this.state === this.states.NO_VOTE || this.state === this.states.NO_VOTE_END,
                    'like-normal-activate': this.state === this.states.VOTED || this.state === this.states.VOTED_END,
                    'active-like': this.state === this.states.VOTE_OP
                }
            }
        },
        methods: {
            removeVote: function removeVote() {
                let i = this.account.witness_votes.indexOf(this.witness.owner);
                if (i > -1) {
                    this.account.witness_votes.splice(i, 1);
                }

                this.$forceUpdate();
            },
            hasVote: function hasVote() {
                let session = this.$props.session;
                let account = this.$props.account;

                if (session && account) {
                    return account.witness_votes.indexOf(this.$props.witness.owner) >= 0;
                }

                return false;
            },
            makeVote: function makeVote(event) {
                if (event) {
                    event.preventDefault();
                }

                if (this.state !== this.states.VOTE_OP) {
                    let that = this;
                    let session = this.$props.session;
                    let witness = this.$props.witness;
                    let username = session ? session.account.username : null;
                    let vote = !this.hasVote();
                    console.log('Voting for', witness.owner, vote);

                    requireRoleKey(username, 'active', function (activeKey, username) {
                        that.state = that.states.VOTE_OP;

                        crea.broadcast.accountWitnessVote(activeKey, username, witness.owner, vote, function (err, result) {
                            if (err) {
                                that.$emit('vote', err);
                            } else {
                                if (vote) {
                                    that.account.witness_votes.push(witness.owner);
                                    that.state = that.states.VOTED_END;
                                } else {
                                    that.removeVote();
                                    that.state = that.states.NO_VOTE_END;
                                }
                                that.$emit('vote', null, result);
                            }
                        });
                    });

                }
            }
        },
        mounted: function mounted() {
            this.state = this.hasVote() ? this.states.VOTED : this.states.NO_VOTE;
        },
        updated: function updated() {
            if (this.state !== this.states.VOTE_OP) {
                this.state = this.hasVote() ? this.states.VOTED : this.states.NO_VOTE;
            }
        }
    }
</script>
