<template>
    <div class="img-user-avatar-profile" v-bind:style="{ 'background-image': 'url(' + ( getDefaultAvatar(account, metadata)) + ')' }"></div>
</template>

<script>
    import R from '../lib/resources';

    export default {
        props: {
            account: {
                type: Object
            },
            metadata: {
                type: Object
            }
        },
        methods: {
            getDefaultAvatar: R.getAvatar
        }
    }
</script>
