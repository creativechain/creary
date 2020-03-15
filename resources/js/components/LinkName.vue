<template>
    <a v-bind:href="'/@' + user" class="link-username">{{ name || '@' + user }}</a>
</template>

<script>
    export default {
        props: {
            user: {
                type: String
            },
            name: {
                type: String
            }
        }
    }
</script>
