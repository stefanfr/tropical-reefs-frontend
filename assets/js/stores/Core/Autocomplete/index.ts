const initialState = () => {
    return {
        autocompleteErrorMessage: null,
    };
};

const getters = {
    autocompleteErrorMessage: state => state.autocompleteErrorMessage,
};

const mutations = {
    SET_AUTOCOMPLETE_ERROR_MESSAGE(state, message) {
        state.autocompleteErrorMessage = message;
    }
};

const actions = {
    async collectAddressAutocomplete({commit}, address) {
        try {
            commit('SET_AUTOCOMPLETE_ERROR_MESSAGE', null);
            const {data} = await this.$apiClient.post('/address/autocomplete', {
                address: address,
            })

            return data;
        } catch (err) {
            if (err.hasOwnProperty('response')) {
                if (err.response.status === 404) {
                    const response = err.response.data;
                    commit('SET_AUTOCOMPLETE_ERROR_MESSAGE', response['error_description']);
                    return {}
                }
                console.log(err.response.data);
            }
            return {};
        }
    }
};

const state = initialState();

const CoreAutocomplete = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

export default CoreAutocomplete;
