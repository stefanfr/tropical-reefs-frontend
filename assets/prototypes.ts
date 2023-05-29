import {AxiosInstance} from "axios";

declare module 'vue/types/vue' {
    interface Vue {
        $apiClient: AxiosInstance,
    }
}

declare module 'vuex/types' {
    interface Store<S> {
        $apiClient: AxiosInstance,
    }
}

const convertPrice = (value: number, locale?: string): string => {
    switch (locale) {
        case 'de_DE':
            return value.toFixed(2).replace('.', ',');
        case 'en_GB':
            return value.toFixed(2).replace(',00', ',-');
        default:
            return value.toFixed(2).replace('.', ',').replace(',00', ',-');
    }
};

Number.prototype['priceFormat'] = function (locale = null): string {
    return convertPrice(this, locale);
};

String.prototype['priceFormat'] = function (locale = null): string {
    return convertPrice(parseFloat(this), locale);
};