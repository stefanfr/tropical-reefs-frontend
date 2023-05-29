<template>
    <div>
        <catalog-product-add-to-cart-pop-up :selected-product-variant="selectedProductVariant" :product="product"
                                            :selected-qty="selectedQty">
            <template v-slot:related-products>
                <slot name="related-products"></slot>
            </template>
            <template v-slot:popup-image>
                <slot name="popup-image"></slot>
            </template>
        </catalog-product-add-to-cart-pop-up>
        <div class="flex gap-3">
            <div class="grid">
                <!--            <div class="product__details-review mt-1">-->
                <!--                <div class="rating rating-sm block mx-1 float-left">-->
                <!--                    <input v-for="(key, value) in [...Array(4).keys()]" aria-label="rating-{{ key+1 }}" disabled type="radio" name="rating-{{ key }}" class="mt-1 mask mask-star-2 bg-accent" :checked="key === 3"/>-->
                <!--                </div>-->
                <!--                <a class="text-sm block mt-1 float-left ml-2" href="#reviews">120 reviews</a>-->
                <!--            </div>-->
                <div class="product__details-delivery block mt-4 md:mt-3">
                    {{ $t('Shipped in 5 - 10 working days') }}
                </div>
            </div>
            <div class="mt-2 text-right grid mr-1 grow">
                <small v-if="this.getDiscountAmount > 0" class="grow product_row__from_price line-through text-sm">
                    {{ this.getProductRegularPrice.priceFormat() }}
                </small>
                <strong class="grow product_row__final_price text-2xl md:text-5xl">
                    {{ this.getProductFinalPrice.priceFormat() }}
                </strong>
            </div>
        </div>
        <div v-if="product['short_description']['html'].length > 0"
             class="product__details-short-description mt-3 sm:hidden lg:block">
            {{ product['short_description']['html'] }}
            <a class="mt-3 text-accent" href="#product-description">{{ ('Read more') }}</a>
        </div>
        <div class="product__details-configuration mt-5 hidden md:block" id="productConfiguration">
            <div class="grid gap-4">
                <template v-if="product['type_id'] === 'configurable'"
                          v-for="attribute in product['configurable_options']">
                    <strong class="block">{{ $t('Select a ') }}{{ attribute['label'].toLowerCase() }}:</strong>
                    <div class="product__details-configuration-items grid grid-cols-4 auto-cols-max gap-2">
                        <label
                                v-for="value in attribute['values']"
                                :for="value['uid']"
                                class="px-4 cursor-pointer product__details-configuration-item"
                                :class="{'__active': selectedSize == value['uid']} || attribute['values'].length === 1"
                        >
                            <input
                                    class="hidden"
                                    type="radio"
                                    :id="value['uid']"
                                    :name="attribute['attribute_uid']"
                                    :value="value['uid']"
                                    v-model="selectedSize"
                            >
                            <span class="block py-2 text-center">
                                {{ value['store_label'] }}
                            </span>
                        </label>
                    </div>
                </template>
                <div class="grid grid-cols-4 xl:grid-cols-5 gap-4 mt-4">
                    <div>
                        {{ product['only_x_left_in_stock'] }}
                        <select v-model="selectedQty" aria-label="select qty" required name="qty" id="qty"
                                class="select select-accent w-full">
                            <option v-for="index in qtySelectorLimit" :value="index">
                                {{ index }}
                            </option>
                        </select>
                    </div>
                    <div class="col-span-3 md:col-span-4 grid gap-3">
                        <button
                                @click="addProductToCart"
                                :disabled="!addToCartIsAvailable || isAddToCartLoading"
                                class="btn btn-outline btn-accent flex"
                        >
                            <svg v-show="isAddToCartLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-accent-content"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="grow">{{ $t('Add to cart') }}</span>
                            <i class="la la-cart-plus la-2x" style="margin-right: 5px"></i>
                        </button>
                    </div>
                    <div class="grid col-span-4 md:col-span-5 grid-cols-2 gap-3">
                        <button
                                @click="addProductToWishlist"
                                class="btn btn-sm btn-outline btn-error"
                        >
                            <svg v-show="isAddToWishlistLoading"
                                 class="animate-spin -ml-1 mr-3 h-5 w-5 text-accent-content"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-show="!isAddToWishlistLoading && !productInWishlist" class="grow">{{ $t('Add to wishlist') }}</span>
                            <span v-show="!isAddToWishlistLoading && productInWishlist" class="grow">{{ $t('Remove from wishlist') }}</span>
                        </button>
                        <button class="btn btn-sm btn-outline btn-info">
                            <span class="grow">{{ $t('Share') }}</span>
                            <i class="la la-share" style="margin-right: 5px"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="sm:fixed hidden bottom-0 left-0 right-0 bg-neutral text-neutral-content z-40">
            <div class="container mx-auto px-5 xl:px-0 py-3 grid grid-cols-1 md:grid-cols-2 gap-5 items-center">
                <div class="grid grid-cols-4">
                    <h4 class="col-span-3 text-lg font-bold">
                        {{ product['name'] }}
                    </h4>
                    <div class="text-center grid">
                        <small v-if="this.getDiscountAmount > 0"
                               class="grow product_row__from_price line-through text-sm">
                            {{ this.getProductRegularPrice.priceFormat() }}
                        </small>
                        <strong class="grow product_row__final_price text-lg">
                            {{ this.getProductFinalPrice.priceFormat() }}
                        </strong>
                    </div>
                </div>
                <div class="flex gap-2 text-base-content">
                    <div v-if="product['type_id'] === 'configurable'">
                        <select
                                v-for="attribute in product['configurable_options']"
                                class="select select-accent w-full"
                                aria-label="Select product option"
                                v-model="selectedSize"
                                :name="attribute['attribute_uid']">
                            <option :value="null" selected>{{ $t('Select a ') }}{{ attribute['label'].toLowerCase() }}
                            </option>
                            <option v-for="value in attribute['values']" :value="value['uid']">
                                {{ value['store_label'] }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <select
                                v-model="selectedQty"
                                aria-label="select qty"
                                required
                                id="qty"
                                class="select select-accent w-full"
                        >
                            <option
                                    v-for="index in qtySelectorLimit"
                                    :value="index"
                            >
                                {{ index }}
                            </option>
                        </select>
                    </div>
                    <div class="grow">
                        <button class="btn btn-outline btn-accent" @click="addProductToCart">
                            <span class="hidden md:block">{{ $t('Add to cart') }}</span>
                            <span class="md:hidden">{{ $t('Add.') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Watch} from "vue-property-decorator";
import Vue from "vue";
import CatalogProductAddToCartPopUp from "./ProductAddToCartPopUp.vue";

@Component({
    name: 'catalog-product-configuration',
    components: {CatalogProductAddToCartPopUp}
})
export default class extends Vue {
    @Prop({required: true}) protected product: Array<object>;

    private selectedSize: string = null;
    private selectedQty: number = 1;

    protected get addToCartIsAvailable(): boolean {
        if (this.product['type_id'] === 'configurable') {
            if (null !== this.selectedProductVariant) {
                return this.selectedProductVariant['stock_status'] === 'IN_STOCK';
            }
            return false;
        }

        return this.product['stock_status'] === 'IN_STOCK';
    }

    protected get selectedProductVariant(): null | object {
        return this.$store.getters['CheckoutQuote/selectedProductVariant'];
    }

    protected get isAddToCartLoading(): null | object {
        return this.$store.getters['CheckoutQuote/addToCartLoading'];
    }

    protected get isAddToWishlistLoading(): null | object {
        return this.$store.getters['Wishlist/addToWishlistLoading'];
    }

    protected get qtySelectorLimit(): Array<Number> {
        let numberArray = [];

        for (let i = 1; i <= 20; i++) {
            numberArray.push(i);
        }

        return numberArray;
    }

    protected get getProductFinalPrice(): Number {
        if (null !== this.selectedProductVariant) {
            return this.selectedProductVariant['price_range']['minimum_price']['final_price']['value'] ?? 0;
        }
        return this.product['price_range']['minimum_price']['final_price']['value'] ?? 0;
    }

    protected get getProductRegularPrice(): Number {
        if (null !== this.selectedProductVariant) {
            return this.selectedProductVariant['price_range']['minimum_price']['regular_price']['value'] ?? 0;
        }

        return this.product['price_range']['minimum_price']['regular_price']['value'] ?? 0;
    }

    protected get getDiscountAmount(): Number {
        if (null !== this.selectedProductVariant) {
            return this.selectedProductVariant['price_range']['minimum_price']['discount']['amount_off'] ?? 0;
        }

        return this.product['price_range']['minimum_price']['discount']['amount_off'] ?? 0;
    }

    protected get productInWishlist(): boolean {
        return this.$store.getters['Wishlist/isInWishlist'](this.product['uid']);
    }

    protected collectProductVariant(uid: string) {
        this.$store.dispatch('CheckoutQuote/collectProductVariant', {
            productUid: this.product['uid'],
            uid: uid
        })
    }

    protected addProductToCart() {
        let payload = {
            qty: this.selectedQty,
            sku: this.product['sku'],
        }

        if (null !== this.selectedSize) {
            payload['options'] = [
                this.selectedSize
            ]
        }

        this.$store.dispatch('CheckoutQuote/addProductToCart', payload);
    }

    protected async addProductToWishlist() {
        if (this.productInWishlist) {
            await this.$store.dispatch('Wishlist/removeProductFromWishlist', this.product['uid']);
            return;
        }

        await this.$store.dispatch('Wishlist/addProductToWishlist', {
            uid: this.product['uid'],
            sku: this.product['sku']
        });
    }

    protected created() {
        if (this.product['type_id'] === 'configurable') {
            if (this.product['configurable_options'].length === 1) {
                this.selectedSize = this.product['configurable_options'][0]['values'][0]['uid'];
            }
        }
    }

    @Watch('selectedSize', {deep: true})
    protected handleSelectedSizeChange(nv) {
        this.collectProductVariant(nv);
    }
}
</script>
