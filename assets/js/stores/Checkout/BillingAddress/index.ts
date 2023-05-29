import {appLocalStorage} from "../../../../shared/services";

const initialState = () => {
    return {
        address: {},
        addressId: null,
        sameAsShippingAddress: appLocalStorage.getItem('sameAsShippingAddress', true),
    };
};

const getters = {
    address: state => state.address,
    addressId: state => state.addressId,
    sameAsShippingAddress: state => state.sameAsShippingAddress,
};

const mutations = {
    SET_ADDRESS(state, address): void {
        state.address = address;
    },
    SET_ADDRESS_ID(state, addressId): void {
        state.addressId = addressId;
    },
    SET_SAME_AS_SHIPPING(state, sameAsShippingAddress): void {
        appLocalStorage.setItem('sameAsShippingAddress', sameAsShippingAddress);
        state.sameAsShippingAddress = sameAsShippingAddress;
    }
};

const actions = {
    setAddress({commit}, address): void {
        commit('SET_ADDRESS', address);
    },
    setAddressId({commit}, addressId): void {
        commit('SET_ADDRESS_ID', addressId);
    },
    async saveAddress({commit, state}): Promise<void> {
        try {
            const {data} = await this.$apiClient.post('/checkout/address/save/billing', {
                address: state.address,
                addressId: state.addressId,
                sameAsShippingAddress: state.sameAsShippingAddress,
            });

            window.location.href = '/checkout/shipment';
        } catch (err) {
            console.log(err);
        }
    },
    async saveCustomerEmail({commit, state}, payload): Promise<void> {
        try {
            const {data} = await this.$apiClient.post('/checkout/address/customer-email', {
                customer_email: payload,
            });

            console.log(data);
        } catch (err) {
        }
    }
};

const state = initialState();

const CheckoutBillingAddress = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

export default CheckoutBillingAddress;
