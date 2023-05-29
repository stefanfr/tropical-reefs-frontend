<template>
    <ul class="col-start-3 col-span-4 steps steps-vertical lg:steps-horizontal">
        <li :class="{'step-accent': currentStep > 0}" class="step">{{ $t('Address') }}</li>
        <li :class="{'step-accent': currentStep > 1}" class="step">{{ $t('Shipping') }}</li>
        <li :class="{'step-accent': currentStep > 2}" class="step">{{ $t('Payment') }}</li>
        <li :class="{'step-accent': currentStep > 3}" class="step">{{ $t('Complete') }}</li>
    </ul>
</template>
<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import Vue from "vue";

@Component({
    name: 'checkout-header'
})
export default class extends Vue {
    @Prop({required: true}) readonly currentStep: number;
}
</script>