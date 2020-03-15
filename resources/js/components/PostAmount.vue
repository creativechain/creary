<template>
    <span class='amount-price'>
        <template v-if='symbefore'>
            {{ assetPart(value, 'sym') }}
            {{ assetPart(value, 'int') + '.'  }}
            <span>{{ assetPart(value, 'dec') }}</span>
        </template>
        <template v-else>
            {{ assetPart(value, 'int') + '.' }}
            <span>{{ assetPart(value, 'dec') }}</span>
            {{ assetPart(value, 'sym') }}
        </template>
    </span>
</template>

<script>
    import { Asset } from '../lib/amount';

    export default {
        props: {
            value: [Number, String, Object],
            symbol: [String],
            symbefore: {
                type: Boolean,
                'default': false
            }
        },
        methods : {
            assetPart: function assetPart(asset, part) {
                asset = Asset.parse(asset);

                switch (part) {
                    case 'int':
                        return asset.toPlainString(null, false).split('.')[0];
                    case 'dec':
                        return asset.toPlainString(null, false).split('.')[1];
                    case 'sym':
                        return this.symbol ? this.symbol : asset.asset.symbol;
                    default:
                        return Asset.parse(asset).toFriendlyString();
                }
            },
        }
    }
</script>
