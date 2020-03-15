<template>
    <a v-bind:href="'/@' + user" class="color-name"><p>{{ name || user }}</p></a>
</template>

<script>
    export default {
        props: {
            user: {
                type: String
            },
            name: {
                type: String
            },
            inline: {
                type: Number,
                'default': 1
            }
        }
    }
</script>
