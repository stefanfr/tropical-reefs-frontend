import './styles/app.scss';
import Vue from "vue";
import Vuex from "vuex";
import axios from "axios";
import {
    CheckoutBillingAddress,
    CheckoutPayment,
    CheckoutQuote,
    CheckoutShipment,
    CheckoutShippingAddress,
    CheckoutTotals,
    CoreAutocomplete,
    CoreToast,
    CustomerAddress,
    CustomerSettings, Wishlist
} from "./js/stores";
import nl_NL from './i18n/nl_NL';
import en_GB from './i18n/en_GB';
import VueI18n from "vue-i18n";
import VueMatchHeights from 'vue-match-heights';
import {configure, localize} from 'vee-validate';
import vee_en_GB from 'vee-validate/dist/locale/en.json';
import vee_nl_NL from 'vee-validate/dist/locale/nl.json';

require('./js/component-register')
require('./prototypes');

localize({vee_en_GB, vee_nl_NL});
localize(`vee_en_GB`);

configure({
    classes: {
        valid: 'input-success',
        failed: 'input-error'
    }
})

const apiClient = axios.create({
    baseURL: '/api/',
    timeout: 10000,
    withCredentials: true,
    headers: {}
});

Vue.use(VueMatchHeights, {
    disabled: [768],
});

Vue.use(Vuex);
Vue.use(VueI18n);
const store = new Vuex.Store({
    strict: false,
});

store.registerModule('Wishlist', Wishlist);
store.registerModule('CoreToast', CoreToast);
store.registerModule('CheckoutQuote', CheckoutQuote);
store.registerModule('CheckoutTotals', CheckoutTotals);
store.registerModule('CheckoutPayment', CheckoutPayment);
store.registerModule('CoreAutocomplete', CoreAutocomplete);
store.registerModule('CheckoutShipment', CheckoutShipment);
store.registerModule('CheckoutBillingAddress', CheckoutBillingAddress);
store.registerModule('CheckoutShippingAddress', CheckoutShippingAddress);
store.registerModule('CustomerAddress', CustomerAddress);
store.registerModule('CustomerSettings', CustomerSettings);

const messages = {
    en_GB,
    nl_NL
};

const i18n = new VueI18n({
    locale: 'nl_NL',
    fallbackLocale: 'en_GB',
    formatFallbackMessages: true,
    silentTranslationWarn: true,
    messages
});

Vue.prototype.$apiClient = apiClient;
Vuex.Store.prototype.$apiClient = apiClient;

new Vue({
    store,
    i18n,
}).$mount('#mainContent');