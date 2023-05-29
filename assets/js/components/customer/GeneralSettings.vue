<template>
    <div class="grid grid-cols-2 gap-6">
        <div class="grid border border-base-300 border-solid rounded-box py-8 px-6 shadow-md">
            <h3 class="text-xl">{{ $t('Personal information') }}</h3>
            <ValidationObserver
                    ref="form"
                    tag="div"
                    class="grid gap-4 mt-4"
            >
                <ValidationProvider
                        :name="$t('Email')"
                        class="w-full"
                        rules="required"
                        v-slot="{ classes }"
                >
                    <input type="text" :class="classes" v-model.trim="customer['email']"
                           class="input input-bordered w-full" :placeholder="$t('Email')">
                </ValidationProvider>
                <ValidationProvider
                        :name="$t('Firstname')"
                        class="w-full"
                        rules="required"
                        v-slot="{ classes }"
                >
                    <input type="text" :class="classes" v-model.trim="customer['firstname']"
                           class="input input-bordered w-full" :placeholder="$t('Firstname')">
                </ValidationProvider>
                <ValidationProvider
                        :name="$t('Lastname')"
                        class="w-full"
                        rules="required"
                        v-slot="{ classes }"
                >
                    <input type="text" :class="classes" v-model.trim="customer['lastname']"
                           class="input input-bordered w-full" :placeholder="$t('Lastname')">
                </ValidationProvider>
                <button @click="saveCustomerDetails"
                        class="btn btn-accent self-end">
                    <svg v-show="saveCustomerDetailsLoading" class="animate-spin w-5 aspect-square"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span v-text="$t('Save details')"/>
                </button>
            </ValidationObserver>
        </div>
        <div class="grid border border-base-300 border-solid rounded-box py-8 px-6 shadow-md">
            <h3 class="text-xl">{{ $t('Change your password') }}</h3>
            <ValidationObserver
                    ref="passwordForm"
                    tag="div"
                    class="grid gap-4 mt-4"
            >
                <ValidationProvider
                        :name="'Current password'"
                        class="w-full"
                        rules="required"
                        v-slot="{ classes }"
                >
                    <input type="password" :class="classes" v-model.trim="customerPassword['password']"
                           name="currentPassword"
                           id="currentPassword"
                           class="input input-bordered w-full" :placeholder="$t('Current password')">
                </ValidationProvider>
                <ValidationProvider
                        :name="'New password'"
                        class="w-full"
                        rules="required"
                        v-slot="{ classes }"
                >
                    <input type="password"
                           name="newPassword"
                           id="newPassword"
                           :class="classes" v-model.trim="customerPassword['newPassword']"
                           class="input input-bordered w-full" :placeholder="$t('New password')">
                </ValidationProvider>
                <ValidationProvider
                        :name="'Repeat password'"
                        class="w-full"
                        rules="required"
                        v-slot="{ classes }"
                >
                    <input type="password"
                           name="confirmPassword"
                           id="confirmPassword"
                           :class="classes" v-model.trim="customerPassword['repeatPassword']"
                           class="input input-bordered w-full" :placeholder="$t('Repeat password')">
                </ValidationProvider>
                <button @click="saveCustomerPassword"
                        class="btn btn-accent self-end">
                    <svg v-show="savePasswordLoading" class="animate-spin w-5 aspect-square"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span v-text="$t('Save password')"/>
                </button>
            </ValidationObserver>
        </div>
    </div>
</template>
<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import {ValidationObserver, ValidationProvider} from "vee-validate";
import Vue from "vue";

@Component({
    name: 'customer-general-settings',
    components: {
        ValidationObserver,
        ValidationProvider,
    }
})
export default class extends Vue {
    $refs!: {
        form: InstanceType<typeof ValidationObserver>;
        passwordForm: InstanceType<typeof ValidationObserver>;
    }

    @Prop({required: true}) customerDetails: object;

    protected customer: object = {
        'firstname': '',
        'lastname': '',
        'email': '',
    };

    protected customerPassword: object = {
        'password': '',
        'newPassword': '',
        'repeatPassword': '',
    };

    protected created() {
        this.customer = Object.assign({}, this.customerDetails);
    }

    protected get savePasswordError(): null | string {
        return this.$store.getters['CustomerSettings/savePasswordError'];
    }

    protected get savePasswordSuccess(): boolean {
        return this.$store.getters['CustomerSettings/savePasswordSuccess'];
    }

    protected get savePasswordLoading(): boolean {
        return this.$store.getters['CustomerSettings/savePasswordLoading'];
    }

    protected get saveCustomerDetailsError(): null | string {
        return this.$store.getters['CustomerSettings/saveCustomerDetailsError'];
    }

    protected get saveCustomerDetailsSuccess(): boolean {
        return this.$store.getters['CustomerSettings/saveCustomerDetailsSuccess'];
    }

    protected get saveCustomerDetailsLoading(): boolean {
        return this.$store.getters['CustomerSettings/saveCustomerDetailsLoading'];
    }

    protected saveCustomerDetails(): void {
        this.$refs.form.validate().then(result => {
            if (result) {
                this.$store.dispatch('CustomerSettings/saveCustomerDetails', this.customer);
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

    protected saveCustomerPassword(): void {
        this.$refs.passwordForm.validate().then(async result => {
            if (result) {
                await this.$store.dispatch('CustomerSettings/savePassword', this.customerPassword);

                if (!this.savePasswordError) {
                    this.customerPassword = {
                        'password': '',
                        'newPassword': '',
                        'repeatPassword': '',
                    };
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
}
</script>