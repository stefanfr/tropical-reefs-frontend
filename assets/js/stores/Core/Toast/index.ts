const initialState = () => {
    return {
        toastMessages: [],
    };
};

const getters = {
    toastMessages: state => state.toastMessages,
};

const mutations = {
    ADD_TOAST_MESSAGE(state, message) {
        state.toastMessages.push(message);
    },
    REMOVE_TOAST_MESSAGE(state, message) {
        if (state.toastMessages.length === 1) {
            state.toastMessages = [];
            return;
        }

        const index = state.toastMessages.indexOf(message);

        if (index > -1) {
            state.toastMessages.splice(index, 1);
        }
    },
};

const actions = {
    addToastMessage({commit}, message) {
        commit('ADD_TOAST_MESSAGE', message);
    },
    removeToastMessage({commit}, message) {
        commit('REMOVE_TOAST_MESSAGE', message);
    }
};

const state = initialState();

const CoreToast = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

export default CoreToast;
