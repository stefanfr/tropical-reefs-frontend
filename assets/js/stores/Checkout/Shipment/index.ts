const initialState = () => {
    return {};
};

const getters = {};

const mutations = {};

const actions = {
    async setShippingMethod({commit}, shippingMethod) {
        try {
            const {data} = await this.$apiClient.post('/checkout/shipment/set-method', {
                'shipping-method': shippingMethod
            });

            console.log(data);
        } catch (err) {
            console.log(err);
        }
    }
};

const state = initialState();

const CheckoutShipment = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

export default CheckoutShipment;
