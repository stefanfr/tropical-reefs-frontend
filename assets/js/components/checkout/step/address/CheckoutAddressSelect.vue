<template>
    <ValidationProvider
            tag="div"
            rules="required"
            v-slot="{ classes }">
        <select
                :class="classes"
                class="mt-6 select select-bordered w-full"
                aria-label="'Select an address'"
                v-model="addressId"
        >
            <option selected disabled value="null">{{ $t('Select an address') }}</option>
            <option
                    v-for="address in customerAddresses"
                    :value="address['id']">
                {{ formatAddress(address) }}
            </option>
            <option value="newAddress">{{ $t('Create an new address') }}</option>
        </select>
    </ValidationProvider>
</template>
<script lang="ts">
import {Component, Prop, Watch} from "vue-property-decorator";
import Vue from "vue";
import {ValidationProvider} from "vee-validate";
import {appLocalStorage} from "../../../../../shared/services";

@Component({
    name: 'checkout-address-select',
    components: {
        ValidationProvider
    },
})
export default class extends Vue {
    @Prop({default: false}) readonly isLoggedIn: boolean;
    @Prop({required: true}) readonly customerAddresses: Array<object>

    protected addressId: number = appLocalStorage.getItem('checkout-billing-address-id', null);

    protected formatAddress(address: object): string {
        let addressArray = [];

        addressArray.push(address['company']);
        addressArray.push(address['firstname']);
        addressArray.push(address['lastname']);
        addressArray.push(address['street'].join(' '));

        return addressArray.join(' ')
    }

    protected created() {
        this.$store.dispatch('CheckoutBillingAddress/setAddressId', this.addressId);
    }

    @Watch('addressId')
    handleAddressIdChange() {
        appLocalStorage.setItem('checkout-billing-address-id', this.addressId);
        this.$store.dispatch('CheckoutBillingAddress/setAddressId', this.addressId);
    }
}
</script>