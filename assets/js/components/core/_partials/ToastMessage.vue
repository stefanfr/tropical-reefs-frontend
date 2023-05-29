<template>
    <div
            :class="{'alert-success': toastMessage['status'] === 'success', 'alert-error': toastMessage['status'] === 'error'}"
            class="alert">
        <div>
            <span v-text="$t(toastMessage['content'])" />
        </div>
    </div>
</template>
<script lang="ts">
import {Component, Prop} from "vue-property-decorator";
import Vue from "vue";

@Component({
    name: 'toast-message'
})
export default class extends Vue {
    @Prop({required: true}) toastMessage: object;

    protected created() {
        setTimeout(() => {
            this.$store.dispatch('CoreToast/removeToastMessage', this.toastMessage);
        }, 2500);
    }
}
</script>