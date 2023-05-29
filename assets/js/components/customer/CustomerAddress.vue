<template>
    <ValidationObserver
            ref="form"
            tag="div"
            class="grid gap-4 mt-4"
    >
        <ValidationProvider
                v-if="customerAddressId"
                :name="'Firstname'"
                rules="required"
                class="w-full"
                v-slot="{ classes }"
        >
            <input type="hidden" :value="customerAddressId" :class="classes">
        </ValidationProvider>
        <div class="flex gap-4">
            <ValidationProvider
                    :name="'Firstname'"
                    rules="required"
                    class="w-full"
                    v-slot="{ classes }"
            >
                <input type="text" v-model.trim="customerAddress['firstname']" :class="classes" class="input input-bordered w-full" placeholder="Firstname" aria-label="Firstname">
            </ValidationProvider>
            <ValidationProvider
                    :name="'Lastname'"
                    rules="required"
                    class="w-full"
                    v-slot="{ classes }"
            >
                <input type="text" v-model.trim="customerAddress['lastname']" :class="classes" class="input input-bordered w-full" placeholder="Lastname" aria-label="Lastname">
            </ValidationProvider>
        </div>
        <div class="flex gap-4">
            <ValidationProvider
                    :name="'Company'"
                    class="w-full"
                    v-slot="{ classes }"
            >
                <input type="text" v-model.trim="customerAddress['company']" :class="classes" class="input input-bordered w-full" placeholder="Company" aria-label="Company">
            </ValidationProvider>
            <ValidationProvider
                    :name="'Phone number'"
                    rules="required"
                    class="w-full"
                    v-slot="{ classes }"
            >
                <input type="text" v-model.trim="customerAddress['telephone']" :class="classes" class="input input-bordered w-full" placeholder="Phone number" aria-label="Phone number">
            </ValidationProvider>
        </div>
        <ValidationProvider
                :name="'Country'"
                tag="div"
                class="flex"
                rules="required"
                v-slot="{ classes }"
        >
            <select :class="classes" v-model="customerAddress['countryCode']" class="select select-bordered w-full">
                <option v-for="country in shippingCountries" :value="country['id']">
                    {{ country['full_name_locale'] }}
                </option>
            </select>
        </ValidationProvider>
        <div class="flex" v-show="!autoCompleteEnabled">
            <ValidationProvider
                    :name="'Street'"
                    class="w-full"
                    rules="required"
                    v-slot="{ classes }"
            >
                <input type="text" :class="classes" v-model.trim="customerAddress['street'][0]" class="input input-bordered w-full" placeholder="Street">
            </ValidationProvider>
            <ValidationProvider
                    :name="'City'"
                    class="w-full"
                    rules="required"
                    v-slot="{ classes }"
            >
                <input type="text" :class="classes" v-model.trim="customerAddress['city']" class="input input-bordered w-full" placeholder="City">
            </ValidationProvider>
        </div>
        <div class="flex gap-4">
            <ValidationProvider
                    :name="'Postcode'"
                    rules="required"
                    class="w-full"
                    v-slot="{ classes }"
            >
                <input @blur="updateAddress()" type="text" v-model.trim="autoComplete['postcode']" :class="classes" class="input input-bordered w-full" placeholder="Postcode" aria-label="Postcode">
            </ValidationProvider>
            <ValidationProvider
                    :name="'House nr.'"
                    rules="required"
                    class="w-full"
                    v-slot="{ classes }"
            >
                <input @blur="updateAddress()" type="text" v-model.trim="autoComplete['houseNr']" :class="classes" class="input input-bordered w-full" placeholder="House nr." aria-label="House nr.">
            </ValidationProvider>
            <ValidationProvider
                    :name="'Add'"
                    class="w-full"
                    v-slot="{ classes }"
            >
                <input @blur="updateAddress()" type="text" v-model.trim="autoComplete['add']" :class="classes" class="input input-bordered w-full" placeholder="Add." aria-label="Add.">
            </ValidationProvider>
        </div>
        <div v-if="autoCompleteErrorMessage">
                <span class="alert alert-error alert-sm">
                    {{ autoCompleteErrorMessage }}
                </span>
            <input class="hidden" id="autoCompleteEnabled" type="checkbox" v-model="autoCompleteEnabled" name="auto-complete-enabled"/>
            <label v-if="autoCompleteEnabled" class="cursor-pointer" for="autoCompleteEnabled">
                {{ 'Fill address manually' }}
            </label>
            <label v-else class="cursor-pointer" for="autoCompleteEnabled">{{ 'Autocomplete address' }}</label>
        </div>
        <div v-else-if="lookupDetails.hasOwnProperty('street')" class="checkout__address-postcode-result bg-base-200 py-6 px-4 rounded-box shadow-md mt-4">
            <strong>{{ 'We found this address' }}:</strong>
            <address class="mt-2">{{ lookupDetails['street'] }} {{ lookupDetails['house_nr_flat'] }}</address>
            <address>{{ lookupDetails['zipcode'] }}, {{ lookupDetails['place'] }}</address>
        </div>
        <div class="flex justify-end">
            <div class="grid grow">
                <label for="subscribe" class="label label-text justify-start gap-2">
                    <input type="checkbox" id="isDefaultShipping" v-model="customerAddress['isDefaultShipping']" class="checkbox checkbox-accent" placeholder="Use as default shipping address" aria-label="Use as default shipping address">
                    <label class="block text-gray-800" for="isDefaultShipping">Is default shipping</label>
                </label>
                <label for="subscribe" class="label label-text justify-start gap-2">
                    <input type="checkbox" id="isDefaultBilling" v-model="customerAddress['isDefaultBilling']" class="checkbox checkbox-accent" placeholder="Use as default billing address" aria-label="Use as default billing address">
                    <label class="block text-gray-800" for="isDefaultBilling">Is default billing</label>
                </label>
            </div>
            <button
                    @click="saveAddress"
                    class="btn btn-accent self-end"
                    value="Save Address"
            >
                <svg v-show="saveAddressLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-accent-content" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span v-show="!saveAddressLoading">
                    {{ 'Save address' }}
            </span>
            </button>
        </div>
    </ValidationObserver>
</template>
<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import Vue from "vue";
import {ValidationObserver, ValidationProvider} from "vee-validate";

@Component({
    name: 'customer-address',
    components: {
        ValidationObserver,
        ValidationProvider,
    }
})
export default class extends Vue {
    $refs!: {
        form: InstanceType<typeof ValidationObserver>;
    }

    @Prop({default: null}) customerAddressId: null | Number;
    @Prop({required: true}) shippingCountries: Array<object>;
    @Prop({default: null}) address: object;

    protected autoCompleteEnabled: boolean = true;

    protected customerAddress: object = {
        firstname: '',
        lastname: '',
        company: '',
        telephone: '',
        street: ['', '', ''],
        postcode: '',
        city: '',
        countryCode: 'NL',
        isDefaultBilling: true,
        isDefaultShipping: true,
    };

    protected autoComplete: object = {
        postcode: '',
        houseNr: '',
        add: ''
    };

    protected lookupDetails: object = {};

    protected get autoCompleteErrorMessage(): string {
        return this.$store.getters['CoreAutocomplete/autocompleteErrorMessage'];
    }

    protected get saveAddressLoading(): string {
        return this.$store.getters['CustomerAddress/saveAddressLoading'];
    }

    protected get saveAddressError(): string {
        return this.$store.getters['CustomerAddress/saveAddressError'];
    }

    protected async getAddressAutoComplete(): Promise<void> {
        this.lookupDetails = await this.$store.dispatch('CoreAutocomplete/collectAddressAutocomplete', this.autoComplete);

        if (this.lookupDetails.hasOwnProperty('street')) {
            this.customerAddress['city'] = this.lookupDetails['place'];
            this.customerAddress['postcode'] = this.lookupDetails['zipcode'];

            this.customerAddress['street'][0] = this.lookupDetails['street'];
            this.customerAddress['street'][1] = this.lookupDetails['house_nr'];
            this.customerAddress['street'][2] = this.lookupDetails['house_nr_bag_letter'];
        }
    }

    protected updateAddress() {
        if (
            this.autoComplete['postcode'].match(/^[1-9][0-9]{3}?(?!sa|sd|ss)[a-z]{2}$/i)
            && this.autoComplete['houseNr'].match(/^.*[0-9].*$/i)
        ) {
            this.autoCompleteEnabled = true;
            this.getAddressAutoComplete();
        }
    }

    protected saveAddress() {
        this.$refs.form.validate().then(result => {
            console.log(result);
            if (result) {
                if (null !== this.customerAddressId) {
                    this.$store.dispatch('CustomerAddress/updateAddress', {
                        addressId: this.customerAddressId,
                        customerAddress: this.customerAddress
                    });
                } else {
                    this.$store.dispatch('CustomerAddress/saveAddress', this.customerAddress);
                }
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

    protected created() {
        if (null !== this.address) {
            this.customerAddress = Object.assign(this.customerAddress, this.address);
            this.autoComplete = {
                postcode: this.address['postcode'],
                houseNr: this.address['street'][1],
                add: this.address['street'][2]
            }
            this.updateAddress();
        }
    }
}
</script>