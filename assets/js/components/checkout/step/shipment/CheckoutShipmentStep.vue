<template>
    <ValidationObserver ref="form" tag="div" class="mt-4 py-8 col-start-3 col-span-4 container border border-base-300 rounded-box shadow-md border-solid grid justify-center">
        <div class="checkout-step-container grid">
            <h2 class="text-2xl mt-4">{{ $t('2. Shipping method') }}</h2>
            <div class="checkout__shipment-container mt-6 grid gap-4 mb-4">
                <div class="checkout__shipment-shipping-methods w-full grid gap-6">
                    <div
                            v-for="shippingMethod in shippingMethods"
                            @click="changeSelectedShippingMethod(shippingMethod)"
                    >
                        <checkout-shipment-method
                                :selected-shipping-method="selectedShippingMethod"
                                :shipping-method="shippingMethod"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="divider lg:divider-vertical"></div>
        <div class="checkout__address-actions mt-4 grid gap-4 grid-cols-2 items-center content-center">
            <a class="link" href="/checkout/address">&lt;&lt; {{ $t('Back to address') }}</a>
            <button
                    @click="nextStep"
                    class="btn text-accent-content btn-accent btn-sm text-right"
            >
                <svg v-show="false" class="animate-spin -ml-1 mr-3 h-5 w-5 text-accent-content" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>
                    {{ $t('Next step') }}&gt;&gt;
                </span>
            </button>
        </div>
    </ValidationObserver>
</template>

<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import Vue from "vue";
import {ValidationObserver} from "vee-validate";

@Component({
    name: 'checkout-shipment-step',
    components: {
        ValidationObserver,
    }
})
export default class extends Vue {
    $refs!: {
        form: InstanceType<typeof ValidationObserver>;
    }

    @Prop({required: true}) shippingMethods: any;
    @Prop({required: true}) inputSelectedShippingMethod: null | object;

    protected selectedShippingMethod: null | object = {
        'carrier_code': null,
    };

    protected created() {
        this.selectedShippingMethod = this.inputSelectedShippingMethod;
    }

    protected changeSelectedShippingMethod(shippingMethod: object) {
        this.selectedShippingMethod = shippingMethod;
        this.$store.dispatch('CheckoutShipment/setShippingMethod', shippingMethod);
    }

    protected nextStep() {
        this.$refs.form.validate().then(result => {
            if (result) {
                window.location.href = '/checkout/payment';
            }
        });
    }
}
</script>