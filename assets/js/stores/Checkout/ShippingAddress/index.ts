const initialState = () => {
    return {
        address: {},
        addressId: null,
    };
};

const getters = {
    address: state => state.address,
    addressId: state => state.addressId,
};

const mutations = {
    SET_ADDRESS(state, address): void {
        state.address = address;
    },
    SET_ADDRESS_ID(state, addressId): void {
        state.addressId = addressId;
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
            const {data} = await this.$apiClient.post('/checkout/address/save/shipping', {
                'address': state.address,
            });

            //
        } catch (err) {
            console.log(err);
        }
    }
};

const state = initialState();

const CheckoutShippingAddress = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

export default CheckoutShippingAddress;
