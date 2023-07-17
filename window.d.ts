// window.d.ts
interface Window {
  Locale: string,
  Config: object,
  dataLayer: Array<object>,
}

import {Store} from "vuex";

declare module "vue/types/vue" {
  interface Vue {
    $store: Store<any>;
  }
}
