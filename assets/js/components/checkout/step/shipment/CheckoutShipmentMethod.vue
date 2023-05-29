<template>
    <div
            :class="{'shadow-red border-accent': selectedShippingMethod.hasOwnProperty('carrier_code') && shippingMethod['carrier_code'] == selectedShippingMethod['carrier_code'] || ''}"
            class="checkout__shipment-shipping-method border-base-200 cursor-pointer flex gap-4 items-center border border-solid py-3 px-8 w-full rounded-box shadow-md"
    >
        <img width="40" height="40" src="/build/assets/images/postnl.png" :alt="shippingMethod['carrier_title']">
        <div class="checkout__shipment-shipping-method-details grow grid">
            <strong>{{ shippingMethod['carrier_title'] }}</strong>
            <small class="text-2xs">{{ shippingMethod['method_title'] }}</small>
        </div>
        <strong v-if="shippingMethod['price_incl_tax']['value']">
            {{ shippingMethod['price_incl_tax']['value'].priceFormat() }}</strong>
        <strong v-else class="text-success">{{ $t('Free') }}</strong>
    </div>
</template>

<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import {ValidationObserver} from "vee-validate";
import Vue from "vue";

@Component({
    name: 'checkout-shipment-method',
    components: {
        ValidationObserver,
    }
})
export default class extends Vue {
    @Prop({required: true}) shippingMethod: object;
    @Prop({required: true}) selectedShippingMethod: null|object;

}
</script>