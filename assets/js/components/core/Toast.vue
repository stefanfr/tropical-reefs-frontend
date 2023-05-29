<template>
    <div class="toast toast-bottom toast-end z-50">
        <toast-message v-for="(toastMessage, key) in toastMessages" :key="key" :toast-message="toastMessage"/>
    </div>
</template>
<script lang="ts">
import {Component} from "vue-property-decorator";
import Vue from "vue";

@Component({
    name: 'toast'
})
export default class extends Vue {
    protected get toastMessages(): Array<object> {
        return this.$store.getters['CoreToast/toastMessages'];
    }
}
</script>