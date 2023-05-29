<template>
    <div>
        <div class="container py-4 grid">
            <h2 class="text-2xl mt-2">{{ $t('Totals') }}</h2>
            <div class="cart__totals-totals grid gap-1 mt-2">
                <div v-for="(total, key) in totals" class="flex justify-between">
                    <span class="grow" v-if="key != Object.keys(totals).length - 1">{{ $t(total['label']) }}</span>
                    <strong class="grow" v-else>{{ $t(total['label']) }}</strong>
                    <span v-if="key != Object.keys(totals).length - 1" v-show="!totalsLoading" :class="{'text-success': total['value'] == 0}" v-html="total['value'] > 0 ? '&euro; ' + total['value'].priceFormat(): $t('Free')"></span>
                    <strong v-else v-show="!totalsLoading" :class="{'text-success': total['value'] == 0}" v-html="total['value'] > 0 ? '&euro; ' + total['value'].priceFormat(): $t('Free')"></strong>
                    <svg v-show="totalsLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-accent-content" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="container py-4 grid">
            <div class="checkout-summary-container grid">
                <h2 class="text-2xl mt-2">{{ $t('Summary') }}</h2>
                <div class="items" style="max-height: 450px; overflow-y: scroll">
                    <div v-for="cartItem in cartItems" class="checkout-summary-item flex justify-center items-center">
                        <div class="flex grow col-span-2 gap-5 border-solid mt-4">
                            <img class="block float-left" width="80" height="80" :src="cartItem['product']['small_image']['url']" :alt="cartItem['product']['name']"/>
                            <div class="grow">
                                <strong class="block text-lg">{{ cartItem['product']['name'] }}</strong>
                                <span v-for="option in cartItem['configurable_options']" class="block">{{ option['option_label'] }}: {{ option['value_label'] }}</span>
                            </div>
                        </div>
                        <div>
                            <strong class="grow text-center m-auto">
                                &euro; {{ cartItem['prices']['row_total_including_tax']['value'].priceFormat() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
import {Component} from "vue-property-decorator";
import Vue from "vue";

@Component({
    name: 'checkout-payment-summary',
    components: {},
})
export default class extends Vue {
    protected get totalsLoading(): boolean {
        return this.$store.getters['CheckoutTotals/totalsLoading'];
    }

    protected get totals(): Array<object> {
        return this.$store.getters['CheckoutTotals/totals'];
    }

    protected get cartItems(): Array<object> {
        return this.$store.getters['CheckoutTotals/cartItems'];
    }

    protected created() {
        this.$store.dispatch('CheckoutTotals/collectCart');
    }
}
</script>