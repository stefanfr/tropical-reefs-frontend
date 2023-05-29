const initialState = () => {
    return {
        wishlistItems: [],
        wishlistItemCount: 0,
        addToWishlistLoading: false,
    };
};

const getters = {
    wishlistItems: state => state.wishlistItems,
    wishlistItemCount: state => state.wishlistItemCount,
    addToWishlistLoading: state => state.addToWishlistLoading,
    isInWishlist: state => function (itemUuid) {
        return state.wishlistItems.hasOwnProperty(itemUuid);
    },
};

const mutations = {
    SET_WISHLIST_ITEMS(state, items) {
        state.wishlistItems = items;
    },
    SET_WISHLIST_ITEM_COUNT(state, count) {
        state.wishlistItemCount = count;
    },
    ADD_TO_WISHLIST_LOADING(state, isLoading) {
        state.addToWishlistLoading = isLoading;
    },
};

const actions = {
    setWishlistItems({commit}, items) {
        commit('SET_WISHLIST_ITEMS', items);
    },
    setWishlistCount({commit}, count) {
        commit('SET_WISHLIST_ITEM_COUNT', count);
    },
    async addProductToWishlist({commit, dispatch}, payload: object) {
        try {
            commit('ADD_TO_WISHLIST_LOADING', true);
            const {data} = await this.$apiClient.post('/catalog/wishlist/product', payload);
            commit('SET_WISHLIST_ITEMS', data['wishlist']['items']);
            commit('SET_WISHLIST_ITEM_COUNT', data['wishlist']['items_count']);

            dispatch('CoreToast/addToastMessage', data, {root: true})
        } catch (err) {
            console.log(err);
        } finally {
            commit('ADD_TO_WISHLIST_LOADING', false);
        }
    },
    async removeProductFromWishlist({state, commit, dispatch}, productIdentifier: string) {
        if (!state.wishlistItems.hasOwnProperty(productIdentifier)) {
            return;
        }

        let itemId = state.wishlistItems[productIdentifier]['id'] || null;
        if (null === itemId) {
            itemId = productIdentifier;
        }

        try {
            commit('ADD_TO_WISHLIST_LOADING', true);
            const {data} = await this.$apiClient.delete('/catalog/wishlist/product/' + itemId);
            commit('SET_WISHLIST_ITEMS', data['wishlist']['items']);
            commit('SET_WISHLIST_ITEM_COUNT', data['wishlist']['items_count']);

            dispatch('CoreToast/addToastMessage', data, {root: true})
        } catch (err) {
            console.log(err);
        } finally {
            commit('ADD_TO_WISHLIST_LOADING', false);
        }
    },
};

const state = initialState();

const Wishlist = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

export default Wishlist;
