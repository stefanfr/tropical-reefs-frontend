const initialState = () => {
    return {
        saveAddressLoading: false,
        saveAddressError: null,
    };
};

const getters = {
    saveAddressLoading: state => state.saveAddressLoading,
    saveAddressError: state => state.saveAddressError,
};

const mutations = {
    SET_SAVE_ADDRESS_LOADING(state, payload) {
        state.saveAddressLoading = payload;
    },
    SET_SAVE_ADDRESS_ERROR(state, payload) {
        state.saveAddressError = payload;
    }
};

const actions = {
    async saveAddress({commit}, customerAddress) {
        try {
            commit('SET_SAVE_ADDRESS_LOADING', true);
            const {data} = await this.$apiClient.post('/customer/address/create', {
                customerAddress: customerAddress,
            });

            window.location.href = '/customer/address';
        } catch (err) {
            if (err.hasOwnProperty('response')) {
                commit('SET_SAVE_ADDRESS_ERROR', err.response.data[0].message);
            }
        } finally {
            commit('SET_SAVE_ADDRESS_LOADING', false);
        }
    },
    async updateAddress({commit}, payload) {
        try {
            commit('SET_SAVE_ADDRESS_LOADING', true);
            const {data} = await this.$apiClient.put('/customer/address/update/' + payload['addressId'], {
                customerAddress: payload['customerAddress'],
            });

            window.location.href = '/customer/address';
        } catch (err) {
            if (err.hasOwnProperty('response')) {
                commit('SET_SAVE_ADDRESS_ERROR', err.response.data[0].message);
            }
        } finally {
            commit('SET_SAVE_ADDRESS_LOADING', false);
        }
    },
};

const state = initialState();

const CustomerAddress = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

export default CustomerAddress;
