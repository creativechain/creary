<template>
    <ul class="ul-recommended-post">
        <li>
            <a href="#0" class="d-flex" v-on:click="makeRecommend">
             <!--Para actilet el parpadeo cuando alguien hace click en el icono, poner esta clases: pre-recommended
             Hay dos estados: icon-recommended-post (por defecto), icon-recommended-post-active cuando esta recomendado -->
                <div v-bind:class="recommendedClasses" v-on:mouseover="onHover(true)" v-on:mouseleave="onHover(false)"></div>
                <span>({{ countRecommended() }})</span>
            </a>
        </li>
    </ul>
</template>

<script>
    import { recommendPost } from '../common/common';
    import { cancelEventPropagation } from '../lib/util';
    import { RECOMMEND_STATE } from './states';

    export default {
        props: {
            post: Object,
            session: [Object, Boolean],
            user: [Object, Boolean]
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
            }
        },
        computed: {
            recommendedClasses: function (){
                return {
                    'icon-recommended-post': !this.hover && this.state === RECOMMEND_STATE.NO_RECOMMENDED,
                    'icon-recommended-post-active': this.state !== RECOMMEND_STATE.NO_RECOMMENDED || this.hover,
                    'pre-recommended': this.state === RECOMMEND_STATE.RECOMMEND_OP
                };
            }
        },
        data: function data() {
            return {
                lang: lang,
                states: RECOMMEND_STATE,
                state: RECOMMEND_STATE.NO_RECOMMENDED,
                hover: false
            };
        },
        methods: {
            onHover: function(hover) {
                this.hover = hover;
            },
            isOwn: function() {
                if (this.session) {
                    return this.session.account.username === this.post.author;
                }

                return false;
            },
            isRecommendedByUser: function() {
                return this.post.reblogged_by.includes(this.user.name);
            },
            countRecommended: function() {
                return this.post.reblogged_by.length;
            },
            makeRecommend: function(event) {
                cancelEventPropagation(event);

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
        },
        updated: function() {
            if (this.state !== RECOMMEND_STATE.RECOMMEND_OP) {
                this.state = this.isRecommendedByUser() ? RECOMMEND_STATE.RECOMMENDED : RECOMMEND_STATE.NO_RECOMMENDED;
            }
        }
    }
</script>
