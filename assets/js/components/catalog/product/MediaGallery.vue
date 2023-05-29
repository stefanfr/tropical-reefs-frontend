<template>
    <VueSlickCarousel :arrows="true" :dots="true">
        <div v-for="image in images" class="px-0 md:px-20">
            <picture>
                <source v-for="source in image['sources']" :media="`(max-width: ${source['size']})`" :srcset="source['url']">
                <img style="aspect-ratio: 1" class="w-full" :src="image['url']" alt="">
            </picture>
        </div>
    </VueSlickCarousel>
</template>
<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import Vue from "vue";
import VueSlickCarousel from 'vue-slick-carousel'
import 'vue-slick-carousel/dist/vue-slick-carousel-theme.css'

@Component({
    name: 'catalog-product-media-gallery',
    components: {VueSlickCarousel},
})
export default class extends Vue {
    @Prop({required: true}) protected images: Array<object>;
}
</script>