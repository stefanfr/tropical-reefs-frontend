<template>
    <div>
        <input type="checkbox" id="addToCartModal" class="modal-toggle" :checked="addedToCart" @change="toggleAddedToCart"/>
        <div class="modal">
            <div class="modal-box" style="width: 50vw; max-width: 50vw">
                <h3 class="font-bold text-lg text-center">{{ $t('Successfully added to cart') }}</h3>
                <div class="py-4">
                    <div class="flex gap-5">
                        <div class="grid auto-rows-min">
                            <slot name="popup-image"></slot>
                        </div>
                        <div class="details grid gap-0 auto-rows-min grow">
                            <strong>{{ product['name'] }}</strong>
                            <span><strong>{{ 'Sku: ' }}</strong> {{ product['sku'] }}</span>
                        </div>
                        <div class="grid gap-2">
                            <label for="addToCartModal" class="btn btn-outline">{{ $t('Continue shopping') }}</label>
                            <a href="/checkout/cart" class="btn btn-outline btn-accent btn-sm">{{ $t('Go to cart') }}</a>
                        </div>
                    </div>
                    <template v-if="product['related_products'].length > 0">
                        <div class="divider lg:divider-vertical" style="margin-top: 2rem">
                            <h3 class="text-3xl font-bold">{{ $t('Related products') }}</h3>
                        </div>
                        <div class="container mx-auto">
                            <div class="product_row grid grid-cols-3 gap-4 mt-10 col-span-6">
                                <slot name="related-products"></slot>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import Vue from "vue";

@Component({
    name: 'catalog-product-add-to-cart-pop-up',
})
export default class extends Vue {
    @Prop({required: true}) protected product: Array<object>;
    @Prop({required: true}) protected selectedQty: Number;
    @Prop({default: null}) protected selectedProductVariant: object | null;

    protected get addedToCart(): boolean {
        return this.$store.getters['CheckoutQuote/productAddedToCart'];
    }

    protected toggleAddedToCart() {
        this.$store.dispatch('CheckoutQuote/toggleProductAddedToCart', !this.addedToCart);
    }
}
</script>