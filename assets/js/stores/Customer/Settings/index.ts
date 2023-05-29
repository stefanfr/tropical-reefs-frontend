const initialState = () => {
    return {
        savePasswordError: null,
        savePasswordSuccess: false,
        savePasswordLoading: false,
        saveCustomerDetailsError: null,
        saveCustomerDetailsSuccess: false,
        saveCustomerDetailsLoading: false,
    };
};

const getters = {
    savePasswordError: state => state.savePasswordError,
    savePasswordSuccess: state => state.savePasswordSuccess,
    savePasswordLoading: state => state.savePasswordLoading,
    saveCustomerDetailsError: state => state.saveCustomerDetailsError,
    saveCustomerDetailsSuccess: state => state.saveCustomerDetailsSuccess,
    saveCustomerDetailsLoading: state => state.saveCustomerDetailsLoading,
};

const mutations = {
    SET_SAVE_PASSWORD_ERROR(state, value) {
        state.savePasswordError = value;
    },
    SET_SAVE_PASSWORD_LOADING(state, value: boolean) {
        state.savePasswordLoading = value;
    },
    SET_SAVE_CUSTOMER_PASSWORD_SUCCESS(state, value: boolean) {
        state.savePasswordSuccess = value;
    },
    SET_SAVE_CUSTOMER_DETAILS_ERROR(state, value) {
        state.saveCustomerDetailsError = value;
    },
    SET_SAVE_CUSTOMER_DETAILS_LOADING(state, value: boolean) {
        state.saveCustomerDetailsLoading = value;
    },
    SET_SAVE_CUSTOMER_DETAILS_SUCCESS(state, value: boolean) {
        state.saveCustomerDetailsSuccess = value;
    },
};

const actions = {
    async savePassword({commit}, payload: object): Promise<void> {
        try {
            commit('SET_SAVE_PASSWORD_ERROR', false);
            commit('SET_SAVE_PASSWORD_LOADING', true);
            const {data} = await this.$apiClient.post('/customer/settings/save/password', {
                currentPassword: payload['password'],
                newPassword: payload['newPassword'],
            });

            console.log(data);
        } catch (err) {
            console.error(err);
            if (err.hasOwnProperty('response')) {
                commit('SET_SAVE_ADDRESS_ERROR', err.response.data[0].message);
            }
        } finally {
            commit('SET_SAVE_PASSWORD_LOADING', false);
        }
    },
    async saveCustomerDetails({commit}, payload: object): Promise<void> {
        try {
            commit('SET_SAVE_CUSTOMER_DETAILS_ERROR', false);
            commit('SET_SAVE_CUSTOMER_DETAILS_LOADING', true);
            const {data} = await this.$apiClient.post('/customer/settings/save/general', {
                customerData: payload,
            });

            console.log(data);
        } catch (err) {
            console.error(err);
            if (err.hasOwnProperty('response')) {
                commit('SET_SAVE_ADDRESS_ERROR', err.response.data[0].message);
            }
        } finally {
            commit('SET_SAVE_CUSTOMER_DETAILS_LOADING', false);
        }
    },
};

const state = initialState();

const CustomerSettings = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

export default CustomerSettings;
