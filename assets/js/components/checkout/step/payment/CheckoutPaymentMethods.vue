<template>
    <div class="checkout__payment-container mt-6 grid gap-6 mb-4">
        <div class="checkout__payment-payment-methods w-full grid gap-3">
            <div
                    v-for="paymentMethod in paymentMethods"
                    :class="{'shadow-red border-accent': paymentMethod['code'] === selectedPaymentMethod}"
                    class="checkout__payment-payment-method border-base-200 cursor-pointer flex gap-4 items-center border border-solid py-3 px-8 w-full rounded-box shadow-md"
                    @click="setSelectedMethod(paymentMethod['code'])"
            >
                <img width="40" height="40" src="/build/assets/images/ideal.png" :alt="paymentMethod['title']">
                <div class="checkout__payment-payment-method-details grow flex justify-between">
                    <span>{{ paymentMethod['title'] }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import Vue from "vue";

@Component({
    name: 'checkout-payment-methods',
    components: {},
})
export default class extends Vue {
    @Prop({required: true}) paymentMethods: Array<object>;

    protected get selectedPaymentMethod(): string {
        return this.$store.getters['CheckoutPayment/selectedPaymentMethod'];
    }

    protected setSelectedMethod(method): void {
        this.$store.dispatch('CheckoutPayment/setSelectedPaymentMethod', method);
    }
}
</script>