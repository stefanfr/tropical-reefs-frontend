import Vue from 'vue';

import Toast from "./components/core/Toast.vue";
import ToastMessage from "./components/core/_partials/ToastMessage.vue";
import HeaderCart from "./components/core/_partials/HeaderCart.vue";
import HeaderWishlist from "./components/core/_partials/HeaderWishlist.vue";
import ProductConfiguration from "./components/catalog/product/ProductConfiguration.vue";
import CategoryProductConfiguration from "./components/catalog/category/CategoryProductConfiguration.vue";
import MediaGallery from "./components/catalog/product/MediaGallery.vue";
import CheckoutFooter from "./components/checkout/CheckoutFooter.vue";
import CheckoutHeader from "./components/checkout/CheckoutHeader.vue";
import CheckoutAddressInput from "./components/checkout/step/address/CheckoutAddressInput.vue";
import CheckoutAddressSelect from "./components/checkout/step/address/CheckoutAddressSelect.vue";
import CheckoutAddressStep from "./components/checkout/step/address/CheckoutAddressStep.vue";
import CheckoutShipmentStep from "./components/checkout/step/shipment/CheckoutShipmentStep.vue";
import CheckoutShipmentMethod from "./components/checkout/step/shipment/CheckoutShipmentMethod.vue";
import CheckoutPaymentCoupon from "./components/checkout/step/payment/CheckoutPaymentCoupon.vue";
import CheckoutPaymentSummary from "./components/checkout/step/payment/CheckoutPaymentSummary.vue";
import CheckoutPaymentActions from "./components/checkout/step/payment/CheckoutPaymentActions.vue";
import CheckoutPaymentMethods from "./components/checkout/step/payment/CheckoutPaymentMethods.vue";
import CustomerAddress from "./components/customer/CustomerAddress.vue";
import CustomerGeneralSettings from "./components/customer/GeneralSettings.vue";

// CORE
Vue.component('toast', Toast);
Vue.component('toast-message', ToastMessage);
Vue.component('header-cart', HeaderCart);
Vue.component('header-wishlist', HeaderWishlist);

// PRODUCT
Vue.component('catalog-product-media-gallery', MediaGallery);
Vue.component('catalog-product-configuration', ProductConfiguration);
Vue.component('catalog-category-product-configuration', CategoryProductConfiguration);

//CUSTOMER
Vue.component('customer-address', CustomerAddress);
Vue.component('customer-general-settings', CustomerGeneralSettings);

//CHECKOUT
Vue.component('checkout-header', CheckoutHeader);
Vue.component('checkout-footer', CheckoutFooter);

//CHECKOUT ADDRESS
Vue.component('checkout-address-step', CheckoutAddressStep);
Vue.component('checkout-address-input', CheckoutAddressInput);
Vue.component('checkout-address-select', CheckoutAddressSelect);

// CHECKOUT SHIPMENT
Vue.component('checkout-shipment-step', CheckoutShipmentStep);
Vue.component('checkout-shipment-method', CheckoutShipmentMethod);

// CHECKOUT PAYMENT
Vue.component('checkout-payment-coupon', CheckoutPaymentCoupon);
Vue.component('checkout-payment-summary', CheckoutPaymentSummary);
Vue.component('checkout-payment-actions', CheckoutPaymentActions);
Vue.component('checkout-payment-methods', CheckoutPaymentMethods);
