<template>
    <div v-bind:class="{ 'img-user-avatar': !profile, 'img-user-avatar-profile': profile}" v-bind:style="{ 'background-image': 'url(' + ( getDefaultAvatar(account)) + ')' }"></div>
</template>

<script>
    import R from '../lib/resources';

    export default {
        props: {
            account: {
                type: Object
            },
            profile: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            getDefaultAvatar: R.getAvatar
        }
    }
</script>
