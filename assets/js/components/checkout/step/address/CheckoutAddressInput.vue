<template>
    <form action="">
        <div class="checkout__address-container mt-6 grid gap-4 mb-4">
            <ValidationProvider
                    class="w-full" rules="required|email" tag="div"
                    :class="{'hidden': isLoggedIn}"
                    v-slot="{ classes }"
                    :name="'Email'"
            >
                <input type="text" v-model.trim.lazy="customerEmail" required="required" :class="classes" class="input input-bordered w-full" placeholder="Email address">
            </ValidationProvider>

            <div class="checkout__address-name flex gap-4 max-w-md">
                <ValidationProvider
                        :name="'Firstname'"
                        rules="required"
                        v-slot="{ classes }"
                >
                    <input type="text" v-model.trim="address['firstname']" :class="classes" required="required" class="input input-bordered w-full" placeholder="Firstname">
                </ValidationProvider>
                <ValidationProvider
                        :name="'Lastname'"
                        rules="required"
                        v-slot="{ classes }"
                >
                    <input type="text" v-model.trim="address['lastname']" :class="classes" required="required" class="input input-bordered w-full" placeholder="Lastname">
                </ValidationProvider>
            </div>

            <ValidationProvider
                    tag="div"
                    class="w-full"
                    :name="'Company'"
                    rules=""
                    v-slot="{ classes }"
            >
                <input type="text" :class="classes" v-model.trim="address['company']" class="input input-bordered w-full" placeholder="Company">
            </ValidationProvider>
            <ValidationProvider
                    tag="div"
                    class="w-full"
                    :name="'Telephone'"
                    rules="required"
                    v-slot="{ classes }"
            >
                <input type="text" :class="classes" v-model.trim="address['telephone']" required="required" class="input input-bordered w-full" placeholder="Phone number">
            </ValidationProvider>
            <ValidationProvider
                    tag="div"
                    class="w-full"
                    :name="'Telephone'"
                    rules="required"
                    v-slot="{ classes }"
            >
                <select :class="classes" v-model.trim="address['country_code']" class="select select-bordered w-full">
                    <option value="NL" selected>Netherlands</option>
                </select>
            </ValidationProvider>
            <ValidationProvider
                    v-show="!autoCompleteEnabled"
                    tag="div"
                    class="w-full"
                    :name="'Street'"
                    rules="required"
                    v-slot="{ classes }"
            >
                <input type="text" :class="classes" v-model.trim="address['street'][0]" class="input input-bordered w-full" placeholder="Street">
            </ValidationProvider>
            <ValidationProvider
                    v-show="!autoCompleteEnabled"
                    tag="div"
                    class="w-full"
                    :name="'City'"
                    rules="required"
                    v-slot="{ classes }"
            >
                <input type="text" :class="classes" v-model.trim="address['city']" class="input input-bordered w-full" placeholder="City">
            </ValidationProvider>

            <div class="flex gap-4 max-w-md">
                <ValidationProvider
                        tag="div"
                        class="w-full"
                        :name="'Postcode'"
                        rules="required"
                        v-slot="{ classes }"
                >
                    <input type="text" :class="classes" v-model.trim="autoComplete['postcode']" name="autoComplete[postcode]" required="required" class="input input-bordered w-full" placeholder="Postcode">
                </ValidationProvider>
                <ValidationProvider
                        tag="div"
                        class="w-full"
                        :name="'House Nr'"
                        rules="required"
                        v-slot="{ classes }"
                >
                    <input type="number" :class="classes" v-model.trim="autoComplete['houseNr']" name="autoComplete[houseNr]" required="required" class="input input-bordered w-full" placeholder="House nr.">
                </ValidationProvider>
                <ValidationProvider
                        tag="div"
                        class="w-full"
                        :name="'Add.'"
                        rules=""
                        v-slot="{ classes }"
                >
                    <input type="text" :class="classes" v-model.trim="autoComplete['houseNrAdd']" name="autoComplete[houseNr]" class="input input-bordered w-full" placeholder="House nr.">
                </ValidationProvider>
            </div>
            <div v-if="autoCompleteErrorMessage">
                <span class="alert alert-error alert-sm">
                    {{ autoCompleteErrorMessage }}
                </span>
                <input class="hidden" id="autoCompleteEnabled" type="checkbox" v-model="autoCompleteEnabled" name="auto-complete-enabled"/>
                <label v-if="autoCompleteEnabled" class="cursor-pointer" for="autoCompleteEnabled">
                    {{ $t('Fill address manually') }}
                </label>
                <label v-else class="cursor-pointer" for="autoCompleteEnabled">{{ $t('Autocomplete address') }}</label>
            </div>
            <div v-else-if="lookupDetails.hasOwnProperty('street')" class="checkout__address-postcode-result bg-base-200 py-6 px-4 rounded-box shadow-md mt-4">
                <strong>{{ $t('We found this address') }}:</strong>
                <address class="mt-2">{{ lookupDetails['street'] }} {{ lookupDetails['house_nr_flat'] }}</address>
                <address>{{ lookupDetails['zipcode'] }}, {{ lookupDetails['place'] }}</address>
            </div>
        </div>
    </form>
</template>
<script lang="ts">
import {Component, Prop, Watch} from "vue-property-decorator";
import Vue from "vue";
import {extend, ValidationProvider} from "vee-validate";
import {email, required} from "vee-validate/dist/rules";
import {appLocalStorage} from "../../../../../shared/services";

extend('required', required);
extend('email', email);
@Component({
    name: 'checkout-address-input',
    components: {
        ValidationProvider,
    }
})
export default class extends Vue {
    @Prop({default: false}) readonly isLoggedIn: boolean;

    protected autoCompleteEnabled: boolean = appLocalStorage.getItem('checkout-autocomplete-enabled', true);
    protected customerEmail: string = appLocalStorage.getItem('checkout-customer-email', '');

    protected autoComplete: object = appLocalStorage.getItem('checkout-autocomplete', {
        postcode: '',
        houseNr: '',
        houseNrAdd: '',
    });

    protected lookupDetails: object = {};

    protected address: object = appLocalStorage.getItem('checkout-shipping-address', {
        country_code: 'NL',
        postcode: '',
        city: '',
        firstname: '',
        lastname: '',
        company: '',
        telephone: '',
        street: ['', '', ''],
    });

    protected get autoCompleteErrorMessage(): string {
        return this.$store.getters['CoreAutocomplete/autocompleteErrorMessage'];
    }

    protected async getAddressAutoComplete(): Promise<void> {
        this.lookupDetails = await this.$store.dispatch('CoreAutocomplete/collectAddressAutocomplete', this.autoComplete);

        if (this.lookupDetails.hasOwnProperty('street')) {
            this.address['city'] = this.lookupDetails['place'];
            this.address['postcode'] = this.lookupDetails['zipcode'];

            this.address['street'][0] = this.lookupDetails['street'];
            this.address['street'][1] = this.lookupDetails['house_nr'];
            this.address['street'][2] = this.lookupDetails['house_nr_bag_letter'];
        }
    }

    protected created() {
        if (
            this.autoComplete['postcode'].match(/^[1-9][0-9]{3}?(?!sa|sd|ss)[a-z]{2}$/i)
            && this.autoComplete['houseNr'].match(/^.*[0-9].*$/i)
        ) {
            this.getAddressAutoComplete();
        }

        this.$store.dispatch('CheckoutBillingAddress/setAddress', this.address);
    }

    @Watch('address', {deep: true})
    handleAddressChange(nv) {
        appLocalStorage.setItem('checkout-shipping-address', nv);
        this.$store.dispatch('CheckoutBillingAddress/setAddress', nv);
    }

    @Watch('customerEmail')
    handleCustomerEmailChange(nv) {
        appLocalStorage.setItem('checkout-customer-email', nv);
        this.$store.dispatch('CheckoutBillingAddress/saveCustomerEmail', nv);
    }

    @Watch('autoCompleteEnabled')
    handleAutoCompleteEnabled() {
        appLocalStorage.setItem('checkout-autocomplete-enabled', this.autoCompleteEnabled);
    }

    @Watch('autoComplete', {deep: true})
    handleAutoCompleteChange(nv: object, ov) {
        if (
            nv['postcode'].match(/^[1-9][0-9]{3}?(?!sa|sd|ss)[a-z]{2}$/i)
            && nv['houseNr'].match(/^.*[0-9].*$/i)
        ) {
            this.autoCompleteEnabled = true;
            appLocalStorage.setItem('checkout-autocomplete', nv);
            this.getAddressAutoComplete();
        }
    }
}
</script>