<template>
    <div>
        <input ref="countryInput" autocomplete="none" type="text" v-bind:placeholder="placeholder" v-on:input="onInput" v-on:focus="onFocus" v-on:focusout="onBlur">
        <div class="autocomplete-items">
            <template v-for="i in matchedItems" >
                <div v-on:click="selectItem(i)">
                    <p v-if="i.textMatched">
                        <strong >{{ i.textMatched }}</strong>{{ i.textUnmatched }} (+{{ i.callingCodes[0] }})
                    </p>
                    <p v-else>
                        {{ i.name }} (+{{ i.callingCodes[0] }})
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
            }
        },
        methods: {
            onFocus: function() {
                this.matchedItems = this.items;
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
                for (let x = 0; x < this.items.length; x++) {
                    let i = this.items[x];
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
                this.$refs.countryInput.value = `${item.name} (+${item.callingCodes[0]})`;
                this.matchedItems = [];
                this.$emit('item', item);
            }
        }
    }
</script>
