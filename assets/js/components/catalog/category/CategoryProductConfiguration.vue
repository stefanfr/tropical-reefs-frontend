<template>
    <div class="flex grow flex-row-reverse gap-2">
        <a
                v-if="product['type_id'] === 'configurable'"
                :href="`/${product['url_key']}`"
                :aria-label="`Configure ${product['name']}`"
                class="btn btn-square btn-accent btn-outline btn-sm"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="w-5 aspect-square">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </a>
        <button
                v-else
                :aria-label="`Add ${ product['name'] } to cart`"
                class="btn btn-square btn-accent btn-outline btn-sm"
                @click="addProductToCart()"
        >
            <svg v-show="isAddToCartLoading" class="animate-spin w-5 aspect-square" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-show="!isAddToCartLoading" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="w-5 aspect-square">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
            </svg>
        </button>
        <button
                :aria-label="`Add ${ product['name'] } to wishlist`"
                class="btn btn-square btn-error btn-outline btn-sm"
                :class="{'btn-active text-':productInWishlist}"
                @click="addProductToWishlist()"
        >
            <svg v-show="isAddToWishlistLoading" class="animate-spin w-5 aspect-square text-error-content"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-show="!isAddToWishlistLoading" :class="{'text-error-content': productInWishlist}"
                 xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 aspect-square">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
            </svg>
        </button>
    </div>
</template>

<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import Vue from "vue";

@Component({
    name: 'catalog-category-product-configuration'
})
export default class extends Vue {
    @Prop({required: true}) protected product: object;

    protected isAddToWishlistLoading: boolean = false;

    protected get isAddToCartLoading(): null | object {
        return this.$store.getters['CheckoutQuote/addToCartLoading'];
    }

    protected get productInWishlist(): boolean {
        return this.$store.getters['Wishlist/isInWishlist'](this.product['uid']);
    }

    protected addProductToCart() {
        let payload = {
            qty: 1,
            sku: this.product['sku'],
        }

        this.$store.dispatch('CheckoutQuote/addProductToCart', payload);
    }

    protected async addProductToWishlist() {
        this.isAddToWishlistLoading = true;
        if (this.productInWishlist) {
            await this.$store.dispatch('Wishlist/removeProductFromWishlist', this.product['uid']);
            this.isAddToWishlistLoading = false;
            return;
        }

        await this.$store.dispatch('Wishlist/addProductToWishlist', {
            uid: this.product['uid'],
            sku: this.product['sku']
        });
        this.isAddToWishlistLoading = false;
    }
}
</script>
