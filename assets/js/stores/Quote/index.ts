const initialState = () => {
    return {
        quoteItemCount: 0,
        wishlistItemCount: 0,
        addToCartLoading: false,
        productAddedToCart: false,
        addToWishlistLoading: false,
        selectedProductVariant: null,
    };
};

const getters = {
    quoteItemCount: state => state.quoteItemCount,
    addToCartLoading: state => state.addToCartLoading,
    productAddedToCart: state => state.productAddedToCart,
    selectedProductVariant: state => state.selectedProductVariant,
};

const mutations = {
    SET_QUOTE_ITEM_COUNT(state, count) {
        state.quoteItemCount = count;
    },
    ADD_TO_CART_LOADING(state, isLoading) {
        state.addToCartLoading = isLoading;
    },
    SET_SELECTED_PRODUCT_VARIANT(state, variant) {
        state.selectedProductVariant = variant;
    },
    SET_PRODUCT_ADDED_TO_CART(state, productAddedToCart) {
        state.productAddedToCart = productAddedToCart;
    },
};

const actions = {
    setQuoteItemCount({commit}, count) {
        commit('SET_QUOTE_ITEM_COUNT', count);
    },
    toggleProductAddedToCart({commit}, productAddedToCart) {
        commit('SET_PRODUCT_ADDED_TO_CART', productAddedToCart);
    },
    async collectProductVariant({commit}, payload: object) {
        try {
            const {data} = await this.$apiClient.post('/catalog/collect/product/variant', payload);

            commit('SET_SELECTED_PRODUCT_VARIANT', data['variant']);
        } catch (err) {
            console.log(err);
        }
    },
    async addProductToCart({commit, dispatch}, payload: object) {
        try {
            commit('ADD_TO_CART_LOADING', true);
            const {data} = await this.$apiClient.post('/catalog/add/product', payload);
            commit('SET_QUOTE_ITEM_COUNT', data['cart']['total_quantity']);
            commit('SET_PRODUCT_ADDED_TO_CART', true);
            dispatch('CoreToast/addToastMessage', {
                status: 'success',
                content: 'Product added to cart',
            }, {root: true})
        } catch (err) {
            if (err.hasOwnProperty('response')) {
                const {data} = err['response'];
                commit('SET_QUOTE_ITEM_COUNT', data['cart']['total_quantity']);
                if (data.hasOwnProperty('user_errors')) {
                    data['user_errors'].forEach((error) => {
                        dispatch('CoreToast/addToastMessage', {
                            status: 'error',
                            content: error['message'],
                        }, {root: true});
                    });
                }
            }
        } finally {
            commit('ADD_TO_CART_LOADING', false);
        }
    },
};

const state = initialState();

const CheckoutQuote = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

export default CheckoutQuote;
