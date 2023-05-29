<template>
    <ValidationObserver ref="form" tag="div" class="mt-4 py-8 col-start-3 col-span-4 container border border-base-300 rounded-box shadow-md border-solid grid justify-center">
        <h2 class="text-2xl mt-4">{{ $t('1. Address') }}</h2>
        <section v-if="customerAddresses.length > 0">
            <checkout-address-select :is-logged-in="isLoggedIn" :customer-addresses="customerAddresses"></checkout-address-select>
        </section>
        <section v-if="customerAddresses.length === 0 || addressId === 'newAddress'">
            <checkout-address-input :is-logged-in="isLoggedIn"></checkout-address-input>
        </section>
        <div class="divider lg:divider-vertical"></div>
        <div class="checkout__address-actions mt-4 grid gap-4 grid-cols-2 items-center content-center">
            <a class="link" href="/checkout/cart">&lt;&lt; {{ $t('Back to cart') }}</a>
            <button
                    class="btn text-accent-content btn-accent btn-sm text-right"
                    @click="nextStep"
            >
                {{ $t('Next step') }}&gt;&gt;
            </button>
        </div>
        <checkout-footer></checkout-footer>
    </ValidationObserver>
</template>
<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import Vue from "vue";
import {ValidationObserver} from "vee-validate";

@Component({
    name: 'checkout-address-step',
    components: {
        ValidationObserver
    }
})
export default class extends Vue {
    $refs!: {
        form: InstanceType<typeof ValidationObserver>;
    }

    @Prop({default: false}) readonly isLoggedIn: boolean;
    @Prop({required: true}) readonly customerAddresses: Array<object>;

    protected get addressId() {
        return this.$store.getters['CheckoutBillingAddress/addressId'];
    }

    protected nextStep() {
        this.$refs.form.validate().then(result => {
            if (result) {
                this.$store.dispatch('CheckoutBillingAddress/saveAddress');
            } else {
                const failedInputElements = document.querySelectorAll('input.failed');
                Array.from(failedInputElements).forEach((el, index) => {
                    if (index === 0) {
                        el.scrollIntoView({behavior: 'smooth', block: 'center', inline: 'start'});
                    }
                    el.classList.add('--shake');
                });
            }
        });
    }
}
</script>