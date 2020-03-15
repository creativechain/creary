<template>
    <div class='amount-price'>
        <span>{{ assetPart(value, 'int') + '.' }}</span>
        <span>{{ assetPart(value, 'dec') }}</span>
        <span>{{ assetPart(value, 'sym') }}</span>
    </div>
</template>

<script>
    import { Asset } from '../lib/amount';

    export default {
        props: {
            value: [Number, String, Object]
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
                        return asset.asset.symbol;
                    default:
                        return Asset.parse(asset).toFriendlyString();
                }
            },
        }
    }
</script>
