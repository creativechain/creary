<template>
    <input v-bind:id="id" class="validate-required" type="text" v-bind:value="value" v-bind:data-role="data-role" v-bind:data-options="data-options" v-bind:placeholder="placeholder" />
</template>

<script>
    export default {
        props: {
            id: {
                type: String
            },
            value: {
                type: String
            },
            'data-role': {
                type: String,
                'default': 'tagsinput'
            },
            placeholder: {
                type: String
            },
            'data-options': {
                type: String
            }
        },
        mounted: function mounted() {
            let el = $('#' + this.id); //this['data-options'] = JSON.parse(this['data-options']);

            console.log('Mounted tags', el, this.id, this.options, this.role); //tags(this.id);
        }
    }
</script>
