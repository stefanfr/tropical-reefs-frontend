<template>
    <div class="checkout__address-actions mt-4 grid gap-4 grid-cols-2 items-center content-center">
        <a class="link" href="/checkout/shipment">&lt;&lt; {{ $t('Back to shipping') }}</a>
        <button
                class="btn text-accent-content btn-accent btn-sm text-right"
                :disabled="placeOrderLoading"
                @click="placeOrder"
        >
            <svg v-show="placeOrderLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-accent-content" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-show="!placeOrderLoading">
                    {{ $t('Place order') }}
            </span>
        </button>
    </div>
</template>
<script lang="ts">
import {Component} from "vue-property-decorator";
import Vue from "vue";

@Component({
    name: 'checkout-payment-actions',
    components: {},
})
export default class extends Vue {
    protected get placeOrderLoading(): boolean {
        return this.$store.getters['CheckoutPayment/placeOrderLoading'];
    }

    protected placeOrder() {
        this.$store.dispatch('CheckoutPayment/placeOrder');
    }
}
</script>