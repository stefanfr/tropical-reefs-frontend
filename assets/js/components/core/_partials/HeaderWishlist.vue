<template>
    <a href="/customer/account/wishlist" class="indicator" aria-label="wishlist">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 aspect-square">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
        </svg>
        <span class="indicator-item badge badge-accent text-accent-content p-2 aspect-square mt-1 mr-2 text-xs">{{ quoteItemCount }}</span>
    </a>
</template>

<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import Vue from "vue";

@Component({
    name: 'header-wishlist'
})
export default class extends Vue {
    @Prop({required: true}) protected wishlist: Array<object>;

    private get quoteItemCount(): number {
        return this.$store.getters['Wishlist/wishlistItemCount'];
    }

    protected created() {
        this.$store.dispatch('Wishlist/setWishlistCount', this.wishlist['items_count']);
        this.$store.dispatch('Wishlist/setWishlistItems', this.wishlist['items']);
    }
}
</script>