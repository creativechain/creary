<template>
    <div class="img-user-avatar" v-bind:style="{ 'background-image': 'url(' + ( getDefaultAvatar(account)) + ')' }"></div>
</template>

<script>
    import R from '../lib/resources';

    export default {
        props: {
            account: {
                type: Object
            }
        },
        methods: {
            getDefaultAvatar: R.getAvatar
        }
    }
</script>
