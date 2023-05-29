const initialState = () => {
    return {
        totals: [],
        cartItems: [],
        couponCode: null,
        totalsLoading: false,
        paymentMethods: [],
        selectedPaymentMethod: {},
    };
};

const getters = {
    totals: state => state.totals,
    cartItems: state => state.cartItems,
    couponCode: state => state.couponCode,
    totalsLoading: state => state.totalsLoading,
    paymentMethods: state => state.paymentMethods,
    selectedPaymentMethod: state => state.selectedPaymentMethod,
};

const mutations = {
    SET_CART_ITEMS(state, items): void {
        state.cartItems = items;
    },
    SET_CART_TOTALS(state, totals): void {
        state.totals = totals;
    },
    SET_CART_COUPON(state, couponCode): void {
        if (null !== couponCode) {
            state.couponCode = couponCode[0]['code'];
            return;
        }
        state.couponCode = null;
    },
    SET_TOTALS_LOADING(state, isLoading): void {
        state.totalsLoading = isLoading;
    },
    SET_CART_PAYMENT_METHODS(state, paymentMethods): void {
        state.paymentMethods = paymentMethods;
    },
    SET_CART_SELECTED_PAYMENT_METHOD(state, paymentMethod): void {
        state.selectedPaymentMethod = paymentMethod;
    },
};

const actions = {
    async collectCart({commit}) {
        try {
            commit('SET_TOTALS_LOADING', true);
            const {data} = await this.$apiClient.get('/checkout/payment/totals');
            commit('SET_CART_ITEMS', data['items']);
            commit('SET_CART_TOTALS', data['totals']);
            commit('SET_CART_COUPON', data['applied_coupons']);
            commit('SET_CART_PAYMENT_METHODS', data['available_payment_methods']);
            commit('SET_CART_SELECTED_PAYMENT_METHOD', data['selected_payment_method']);
        } catch (err) {
            console.log(err);
        } finally {
            commit('SET_TOTALS_LOADING', false);
        }
    }
};

const state = initialState();

const CheckoutTotals = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

export default CheckoutTotals;
