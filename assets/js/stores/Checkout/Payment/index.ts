const initialState = () => {
    return {
        couponIsValid: false,
        placeOrderLoading: false,
        couponErrorMessage: null,
        selectedPaymentMethod: null,
    };
};

const getters = {
    couponIsValid: state => state.couponIsValid,
    placeOrderLoading: state => state.placeOrderLoading,
    couponErrorMessage: state => state.couponErrorMessage,
    selectedPaymentMethod: state => state.selectedPaymentMethod,
};

const mutations = {
    SET_COUPON_IS_VALID(state, couponIsValid) {
        state.couponIsValid = couponIsValid;
    },
    SET_COUPON_ERROR_MESSAGE(state, couponErrorMessage) {
        state.couponErrorMessage = couponErrorMessage;
    },
    SET_PLACE_ORDER_LOADING(state, isLoading) {
        state.placeOrderLoading = isLoading;
    },
    SET_SELECTED_PAYMENT_METHOD(state, selectedPaymentMethod) {
        state.selectedPaymentMethod = selectedPaymentMethod;
    },
};

const actions = {
    setCouponIsValid({commit}, couponIsValid) {
        commit('SET_COUPON_IS_VALID', couponIsValid);
    },
    async applyCoupon({commit, dispatch}, couponCode) {
        try {
            const {data} = await this.$apiClient.get('/checkout/payment/coupon/' + couponCode);
            commit('SET_COUPON_IS_VALID', true);
            dispatch('CheckoutTotals/collectCart', {}, {root: true});

        } catch (err) {
            if (err.hasOwnProperty('response')) {
                commit('SET_COUPON_ERROR_MESSAGE', err.response.data[0].message);
            }
        }
    },
    async removeCoupon({commit, dispatch}) {
        try {
            const {data} = await this.$apiClient.delete('/checkout/payment/coupon');
            commit('SET_COUPON_IS_VALID', false);
            dispatch('CheckoutTotals/collectCart', {}, {root: true});
        } catch (err) {
            if (err.hasOwnProperty('response')) {
                commit('SET_COUPON_ERROR_MESSAGE', err.response.data[0].message);
            }
        }
    },
    async placeOrder({commit, dispatch}) {
        try {
            commit('SET_PLACE_ORDER_LOADING', true);
            const {data} = await this.$apiClient.post('/checkout/payment/place');

            if (data.hasOwnProperty('redirect_url')) {
                window.location.href = data.redirect_url;
                return;
            }

            window.location.href = '/checkout/success';
        } catch (err) {

        } finally {
            commit('SET_PLACE_ORDER_LOADING', false);
        }
    },
    async setSelectedPaymentMethod({commit}, payload) {
        try {
            const {data} = await this.$apiClient.post('/checkout/payment/set-method', {
                method_code: payload,
            });
            console.log(data);
            commit('SET_SELECTED_PAYMENT_METHOD', payload);
        } catch (err) {

        }
    }
};

const state = initialState();

const CheckoutPayment = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

export default CheckoutPayment;
