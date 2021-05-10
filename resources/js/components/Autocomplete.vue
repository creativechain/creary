<template>
    <div>
        <input ref="countryInput" autocomplete="none" type="text" v-bind:placeholder="placeholder" v-on:input="onInput" v-on:focus="onFocus" v-on:focusout="onBlur">
        <div class="autocomplete-items">
            <template v-for="i in matchedItems" >
                <div v-on:click="selectItem(i)">
                    <p v-if="i.textMatched">
                        <strong >{{ i.textMatched }}</strong>{{ i.textUnmatched }} (+{{ i.prefix }})
                    </p>
                    <p v-else>
                        {{ i.name }} (+{{ i.prefix }})
                    </p>

                </div>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            items: Array,
            placeholder: String,
        },
        data: function() {
            return {
                value: '',
                selectedItem: null,
                matchedItems: [],
                sItems: []
            }
        },
        mounted() {
            let prefixCodes = [];
            for (const i of this.items) {
                for (const c of i.callingCodes) {
                    prefixCodes.push({
                        name: i.name,
                        prefix: c
                    })
                }
            }

            this.sItems = prefixCodes;
        },
        methods: {
            onFocus: function() {
                this.matchedItems = this.sItems;
            },
            onBlur: function () {
                //this.matchedItems = [];
                let that = this;
                let phonePrefix = this.$refs.countryInput.value;
                this.$emit('item', phonePrefix);
                setTimeout(function () {
                    that.matchedItems = [];
                }, 200);

            },
            onInput: function (event) {
                let search = event.target.value.toLowerCase();
                let filteredItems = [];
                for (let x = 0; x < this.sItems.length; x++) {
                    let i = this.sItems[x];
                    if (i.name.toLowerCase().startsWith(search)) {
                        i.textMatched = i.name.substr(0, search.length);
                        i.textUnmatched = i.name.substr(search.length, i.name.length);
                        filteredItems.push(i);
                    }
                }

                this.matchedItems = filteredItems;

            },
            selectItem: function (item) {
                this.selectedItem = item;
                this.$refs.countryInput.value = `${item.name} (+${item.prefix})`;
                this.matchedItems = [];
                this.$emit('item', item);
            }
        }
    }
</script>
