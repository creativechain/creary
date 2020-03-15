<template>
    <div>
        <template v-if="session && !isOwn()">
            <div v-bind:class="actionClasses" v-on:click="makeRecommend()">
                <div v-bind:class="recommendedClasses"></div>
            </div>
        </template>
        <div v-if="feed && recommendedBy()" class="row-recommended">
            <p>
                <img src="/img/recommended/recommended_icon_1.svg" alt=""> {{ lang.PUBLICATION.RECOMMENDED_BY }} <a v-bind:href="'/@' + recommendedBy()">@{{ recommendedBy() }}</a>
                <template v-if="countRecommended() > 1">
                    {{ lang.COMMON.AND + " " + countRecommended() + " " + lang.COMMON.MORE }}
                </template>
            </p>
        </div>
    </div>
</template>

<script>
    import { RECOMMEND_STATE } from './states';
    import { recommendPost } from "../common/common";

    export default {
        props: {
            post: Object,
            session: [Object, Boolean],
            user: [Object, Boolean],
            feed: {
                type: Boolean,
                'default': false
            }
        },
        watch: {
            post: {
                immediate: true,
                deep: true,
                handler: function handler(newVal, oldVal) {
                    this.$forceUpdate();
                }
            },
            user: {
                immediate: true,
                deep: true,
                handler: function handler(newVal, oldVal) {
                    this.$forceUpdate();
                }
            },
            feed: {
                immediate: true,
                handler: function handler(newVal, oldVal) {
                    this.$forceUpdate();
                }
            }
        },
        computed: {
            recommendedClasses: function (){
                return {
                    'icon-recommended': true,
                    'hover-recommended': this.state !== RECOMMEND_STATE.RECOMMENDED,
                    'my-active': this.state === RECOMMEND_STATE.RECOMMENDED
                };
            },
            actionClasses: function (){
                return {
                    'hidden': !this.hover && this.state !== RECOMMEND_STATE.RECOMMEND_OP,
                    'row-circle-recommended': true,
                    'pre-recommended': this.state === RECOMMEND_STATE.RECOMMEND_OP,
                    'parpadeo': this.state === RECOMMEND_STATE.RECOMMEND_OP
                };
            }
        },
        data: function data() {
            return {
                lang: lang, //On window.lang
                states: RECOMMEND_STATE,
                state: RECOMMEND_STATE.NO_RECOMMENDED,
                hover: false
            };
        },
        methods: {
            isOwn: function() {
                if (this.session) {
                    return this.session.account.username === this.post.author;
                }

                return false;
            },
            isRecommendedByUser: function() {
                return this.post.reblogged_by.includes(this.user.name);
            },
            recommendedBy: function () {
                let that = this;
                let recommended = false;
                this.user.followings.forEach(function (followed) {
                    if (that.post.reblogged_by.includes(followed)) {
                        recommended = followed;
                    }
                });

                return recommended;
            },
            countRecommended: function() {
                let that = this;
                let recommendeds = 0;
                this.user.followings.forEach(function (followed) {
                    if (that.post.reblogged_by.includes(followed)) {
                        recommendeds += 1;
                    }
                });

                //sconsole.log(this.post.author + '/' + this.post.permlink, recommendeds);
                return recommendeds;
            },
            makeRecommend: function() {
                let that = this;
                if (!this.isRecommendedByUser() && this.state !== this.states.RECOMMEND_OP) {
                    this.state = RECOMMEND_STATE.RECOMMEND_OP;
                    recommendPost(that.post.author, that.post.permlink, true, function (err, result) {
                        if (err) {
                            that.state = RECOMMEND_STATE.NO_RECOMMENDED;
                            that.$emit('recommend', err, null, that.post);
                        } else {
                            that.post.reblogged_by.push(that.session.account.username);
                            that.state = RECOMMEND_STATE.RECOMMENDED;

                            //Notify
                            that.$emit('recommend', null, result, that.post);
                        }
                    })
                }
            }
        },
        mounted: function() {
            let that = this;
            this.state = this.isRecommendedByUser() ? RECOMMEND_STATE.RECOMMENDED : RECOMMEND_STATE.NO_RECOMMENDED;
            //efect recommended
            $(this.$el).parent().hover(
                function() {
                    that.hover = true;
                    that.$forceUpdate();
                }, function() {
                    that.hover = false;
                    that.$forceUpdate();
                }
            );
        },
        updated: function() {
            if (this.state !== RECOMMEND_STATE.RECOMMEND_OP) {
                this.state = this.isRecommendedByUser() ? RECOMMEND_STATE.RECOMMENDED : RECOMMEND_STATE.NO_RECOMMENDED;
            }
        }
    }
</script>
