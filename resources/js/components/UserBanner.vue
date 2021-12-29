<template>
    <div class="banner-user" v-bind:style="{ 'background-color': '#EAEAEA', 'background-image': 'url(' + getBannerUrl() + ')' }"></div>
</template>

<script>

    export default {
        props: {
            metadata: {
                type: Object
            }
        },
        methods: {
            hasBanner: function () {
                return this.$props.metadata.other.banner && this.$props.metadata.other.banner.url;
            },
            getBannerUrl: function () {
                return (this.hasBanner() ? this.$props.metadata.other.banner.url : '')
            }
        }
    }
</script>
