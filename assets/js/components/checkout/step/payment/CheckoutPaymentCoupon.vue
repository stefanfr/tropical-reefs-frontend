<template>
    <div class="flex gap-2 items-center">
        <div class="input-group">
            <input
                    aria-label="apply coupon"
                    type="text"
                    :placeholder="'Coupon code'"
                    :class="{'btn-error': couponErrorMessage, 'btn-accent': couponIsValid}"
                    class="input input-bordered input-sm"
                    v-model="couponCode"
            />
            <button
                    v-if="couponIsValid"
                    class="btn btn-error btn-sm"
                    @click="removeCoupon()"
            >
                {{ $t('Remove') }}
            </button>
            <button
                    v-else
                    :class="{'btn-error': couponErrorMessage, 'btn-accent': !couponErrorMessage}"
                    class="btn btn-sm"
                    @click="applyCoupon()"
            >
                {{ $t('Apply') }}
            </button>
        </div>
        <small class="text-error">{{ couponErrorMessage }}</small>
    </div>
</template>
<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import {ValidationObserver} from "vee-validate";
import Vue from "vue";

@Component({
    name: 'checkout-payment-coupon',
    components: {
        ValidationObserver,
    }
})
export default class extends Vue {
    @Prop({default: null}) inputCouponCode: string;
    protected couponCode: string = null;

    protected get quoteCouponCode() {
        return this.$store.getters['CheckoutTotals/couponCode'];
    }

    protected get couponIsValid(): boolean {
        return this.$store.getters['CheckoutPayment/couponIsValid'];
    }

    protected get couponErrorMessage(): string {
        return this.$store.getters['CheckoutPayment/couponErrorMessage'];
    }

    protected applyCoupon(): void {
        this.$store.dispatch('CheckoutPayment/applyCoupon', this.couponCode);
    }

    protected async removeCoupon(): Promise<void> {
        await this.$store.dispatch('CheckoutPayment/removeCoupon');
        this.couponCode = null;
    }

    protected created(): void {
        if (this.inputCouponCode) {
            this.$store.dispatch('CheckoutPayment/setCouponIsValid', true);
            this.couponCode = this.inputCouponCode;
        }
    }
}
</script>