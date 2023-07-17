export enum CartEvents {
  ADD = 'add_to_cart',
  REMOVE = 'remove_from_cart'
}

export enum WishlistEvents {
  ADD = 'add_to_wishlist',
  REMOVE = 'remove_from_wishlist'
}

export class DataLayer {
  static instance: DataLayer;

  dataLayer: Array<object>;
  static currency: string = 'EUR';
  static config: object = {
    'item_id': 'product_id',
    'item_name': 'name',
    'item_variant_id': '',
    'item_brand': 'manufacturer',
    'item_categories': 'category_names',
    'item_variant': '',
    'price': 'final_price_incl_tax',
    'old_price': 'price_incl_tax'
  };

  /**
   * Create new dataLayer element when there is none
   */
  constructor() {
    this.dataLayer = window.dataLayer || [];
  }

  /**
   * Product list used to store list items for observable implementation
   *
   * @protected
   */
  protected static productList: Array<object> = [];

  /**
   * listType used to store the list type for observable implementation
   *
   * @protected
   */
  protected static listType: string = '';

  /**
   * Default config for observable item list
   *
   * @protected
   */
  protected static observableConfig: object = {
    'listSelector': 'impressions-observable',
    'itemsPerRowDesktop': 4,
    'itemsPerRowMobile': 2
  };

  /**
   *
   * @protected
   */
  protected static isMobile: boolean = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

  /**
   *
   * @param config
   * @param currency
   */
  static init(config?: object, currency?: string) {
    if (DataLayer.instance) {
      return DataLayer.instance;
    }

    if (config) {
      DataLayer.config = {
        ...DataLayer.config,
        ...config
      };
    }

    if (currency) {
      DataLayer.currency = currency;
    }

    DataLayer.instance = new DataLayer();

    return DataLayer.instance;
  }

  /**
   *
   * @param value
   */
  static dataLayerValue(value: any): any {
    return value || '';
  }

  /**
   * Reset dataLayer
   */
  static resetDataLayer(): void {
    window.dataLayer.push({'ecommerce': null});
  }

  /**
   *
   * @param value
   */
  static formatPrice(value: number | string): number {
    const price = typeof value === 'string' ? parseFloat(value) : value;
    const multiplier = Math.pow(10, 2);
    return (Math.round(price * multiplier) / multiplier);
  }

  /**
   *
   * @param categories
   * @param position
   */
  static resolveCategory(categories: Array<string>, position: number): string {
    return categories[position] || '';
  }

  /**
   *
   * @param oldPrice
   * @param price
   */
  static getSalePrice(oldPrice: number, price: number): number | string {
    return oldPrice !== price ? oldPrice - price : '';
  }

  /**
   *
   * @param product
   */
  static defaultProductInformation(product: object): object {
    const config = DataLayer.config;

    const productData = {
      'item_name': DataLayer.dataLayerValue(product[config['item_name']]),
      'item_id': DataLayer.dataLayerValue(product[config['item_id']]),
      'item_brand': DataLayer.dataLayerValue(product[config['item_brand']]),
      'item_variant': DataLayer.dataLayerValue(product[config['item_variant']]),
      'currency': DataLayer.currency,
      'price': DataLayer.dataLayerValue(DataLayer.formatPrice(product[config['price']])),
      'discount': DataLayer.getSalePrice(DataLayer.formatPrice(product[config['old_price']]), DataLayer.formatPrice(product[config['price']]))
    };

    let categoryData = {};
    for (let i = 0; i < 5; i++) {
      categoryData[`item_category${i > 0 ? i + 1 : ''}`] = DataLayer.resolveCategory(Object.prototype.hasOwnProperty.call(product, config['item_categories']) ? product[config['item_categories']] : [], i);
    }

    return {
      ...productData,
      ...categoryData
    };
  }

  /**
   *
   * @param product
   */
  static quoteProductInformation(product: object): object {
    const config = DataLayer.config;

    const productData = {
      'item_name': DataLayer.dataLayerValue(product[config['item_name']]),
      'item_id': DataLayer.dataLayerValue(product['item_id']),
      'item_brand': DataLayer.dataLayerValue(product['extension_attributes'][config['item_brand']]),
      'item_variant': DataLayer.dataLayerValue(product['extension_attributes'][config['item_variant']]),
      'currency': DataLayer.currency,
      'price': DataLayer.dataLayerValue(DataLayer.formatPrice(product['price_incl_tax'])),
      'discount': DataLayer.getSalePrice(DataLayer.formatPrice(product['extension_attributes']['from_price']), DataLayer.formatPrice(product['price_incl_tax'])),
      'quantity': DataLayer.dataLayerValue(product['qty'])
    };

    let categoryData = {};

    //Check if category data is present on the quote item
    const categoryItemsRaw = product['extension_attributes'][config['item_categories']];
    if (categoryItemsRaw) {
      const categoryItems = typeof categoryItemsRaw === 'object' ? categoryItemsRaw : JSON.parse(categoryItemsRaw);
      for (let i = 0; i < 5; i++) {
        categoryData[`item_category${i > 0 ? i + 1 : ''}`] = DataLayer.resolveCategory(categoryItems, i);
      }
    }

    return {
      ...productData,
      ...categoryData
    };

  }

  /**
   *
   * @param products
   * @param listType
   * @param config
   */
  addObservableViewItemsList(products: Array<object>, listType?: string, config: null | object = null): void {
    if (config) {
      DataLayer.observableConfig = {
        ...DataLayer.observableConfig,
        ...config
      };
    }

    DataLayer.productList = products;
    DataLayer.listType = listType;

    const observerOptions = {
      root: null,
      rootMargin: '0px',
      threshold: 0
    }

    const observer = new IntersectionObserver(DataLayer.addViewItemsListEntries, observerOptions);
    const rootElement = document.getElementById(DataLayer.observableConfig['listSelector'] || 'impressions-observable');
    const itemsPerRow = DataLayer.isMobile ? DataLayer.observableConfig['itemsPerRowMobile'] : DataLayer.observableConfig['itemsPerRowDesktop'];

    if (rootElement) {
      const observeTargets = rootElement.querySelectorAll('[data-index]');
      for (let i = 0; i < observeTargets.length; i++) {
        if (i % itemsPerRow === 0) {
          observer.observe(observeTargets[i]);
        }
      }
    }
  }

  /**
   *
   * @param entries
   * @param observer
   */
  static addViewItemsListEntries(entries: IntersectionObserverEntry[], observer: IntersectionObserver): void {
    const productsClone = DataLayer.productList;
    const itemsPerRow = DataLayer.isMobile ? DataLayer.observableConfig['itemsPerRowMobile'] : DataLayer.observableConfig['itemsPerRowDesktop'];

    entries.map(entry => {
      if (entry.isIntersecting && entry.target.hasAttribute('data-index')) {
        const itemIndex = parseInt(entry.target.getAttribute('data-index'));
        DataLayer.init().addViewItemsList(productsClone.slice(itemIndex, itemIndex + itemsPerRow), DataLayer.listType, itemIndex);

        observer.unobserve(entry.target);
      }
    });
  }

  /**
   *
   * @param products
   * @param listType
   * @param index
   */
  addViewItemsList(products: Array<object>, listType?: string, index: number = 0): void {
    if (!window.dataLayer.filter(e => e['event'] === 'view_item_list').length) {
      DataLayer.resetDataLayer();
    }

    let items: Array<object> = [];

    products.forEach((product: object) => {
      index++;

      const productData = {
        'item_list_id': listType.replace(/ /g, '_').toLowerCase(),
        'item_list_name': listType,
        'index': index,
      };

      items.push(Object.assign({}, productData, DataLayer.defaultProductInformation(product)));
    });

    window.dataLayer.push({
      'event': 'view_item_list',
      'ecommerce': {
        'items': items
      }
    });
  }

  /**
   *
   * @param product
   * @param index
   * @param listType
   */
  addSelectItem(product: object, index: number | string, listType?: string): void {
    DataLayer.resetDataLayer();

    let items: Array<object> = [];

    const productData = {
      'item_list_id': listType.replace(/ /g, '_').toLowerCase(),
      'item_list_name': listType,
      'index': index,
    };

    items.push(Object.assign({}, productData, DataLayer.defaultProductInformation(product)));

    window.dataLayer.push({
      'event': 'select_item',
      'ecommerce': {
        'currency': DataLayer.currency,
        'items': items
      }
    });
  }

  /**
   *
   * @param product
   */
  addViewItem(product: object): void {
    DataLayer.resetDataLayer();

    window.dataLayer.push({
      'event': 'view_item',
      'ecommerce': {
        'currency': DataLayer.currency,
        'items': DataLayer.defaultProductInformation(product)
      }
    });
  }

  /**
   * Add or remove items from cart
   *
   * @param product
   * @param event
   * @param qty
   */
  addAddToCart(product: object, event: CartEvents = CartEvents.ADD, qty: number = 1): void {
    DataLayer.resetDataLayer();

    let items: Array<object> = [];

    const productData = {
      'quantity': qty,
    };

    if (event === CartEvents.ADD) {
      items.push(Object.assign({}, productData, DataLayer.defaultProductInformation(product)));
    } else {
      items.push(Object.assign({}, productData, DataLayer.quoteProductInformation(product)));
    }

    window.dataLayer.push({
      'event': event,
      'ecommerce': {
        'currency': DataLayer.currency,
        'items': items
      }
    });
  }

  /**
   *
   * Add or remove items from wishlist
   *
   * @param product
   * @param event
   */
  addAddToWishlist(product: object, event: WishlistEvents = WishlistEvents.ADD): void {
    DataLayer.resetDataLayer();

    window.dataLayer.push({
      'event': event,
      'ecommerce': {
        'items': DataLayer.defaultProductInformation(product)
      }
    });
  }

  /**
   *
   * @param products
   * @param cartValue
   * @param couponCode
   */
  addViewCart(products: Array<object>, cartValue: number, couponCode: string = '') {
    DataLayer.resetDataLayer();

    let items: Array<object> = [];

    products.forEach((product: object, index: number) => {
      const productData = {
        'index': index,
        'coupon': couponCode
      };

      items.push(Object.assign({}, productData, DataLayer.quoteProductInformation(product)));
    });

    window.dataLayer.push({
      'event': 'view_cart',
      'ecommerce': {
        'currency': DataLayer.currency,
        'value': DataLayer.formatPrice(cartValue),
        'items': items
      }
    });
  }

  /**
   *
   * @param products
   * @param couponCode
   */
  addBeginCheckout(products: Array<object>, couponCode: string = '') {
    DataLayer.resetDataLayer();

    let items: Array<object> = [];

    products.forEach((product: object, index: number) => {
      const productData = {
        'index': index,
        'coupon': couponCode
      };

      items.push(Object.assign({}, productData, DataLayer.quoteProductInformation(product)));
    });

    window.dataLayer.push({
      'event': 'begin_checkout',
      'ecommerce': {
        'items': items
      }
    });
  }

  /**
   *
   * @param products
   * @param cartValue
   * @param shippingTier
   * @param couponCode
   */
  addAddShippingInfo(products: Array<object>, cartValue: number, shippingTier: string = '', couponCode: string = '') {
    DataLayer.resetDataLayer();

    let items: Array<object> = [];

    products.forEach((product: object, index: number) => {
      const productData = {
        'index': index,
        'coupon': couponCode
      };

      items.push(Object.assign({}, productData, DataLayer.quoteProductInformation(product)));
    });

    window.dataLayer.push({
      'event': 'add_shipping_info',
      'ecommerce': {
        'currency': DataLayer.currency,
        'value': DataLayer.formatPrice(cartValue),
        'coupon': couponCode,
        'shipping_tier': shippingTier,
        'items': items
      }
    });
  }

  /**
   *
   * @param products
   * @param cartValue
   * @param paymentType
   * @param couponCode
   */
  addAddPaymentInfo(products: Array<object>, cartValue: number, paymentType: string = '', couponCode: string = '') {
    DataLayer.resetDataLayer();

    let items: Array<object> = [];

    products.forEach((product: object, index: number) => {
      const productData = {
        'index': index,
        'coupon': couponCode
      };

      items.push(Object.assign({}, productData, DataLayer.quoteProductInformation(product)));
    });

    window.dataLayer.push({
      'event': 'add_payment_info',
      'ecommerce': {
        'currency': DataLayer.currency,
        'value': DataLayer.formatPrice(cartValue),
        'coupon': couponCode,
        'payment_type': paymentType,
        'items': items
      }
    });
  }

  /**
   *
   * @param products
   * @param transactionId
   * @param affiliation
   * @param cartValue
   * @param tax
   * @param shipping
   * @param couponCode
   */
  addPurchase(products: Array<object>, transactionId: string | number, affiliation: string = '', cartValue: number, tax: number, shipping: number, couponCode: string = '') {
    DataLayer.resetDataLayer();

    let items: Array<object> = [];

    products.forEach((product: object, index: number) => {
      const productData = {
        'index': index,
        'coupon': couponCode,
        'affiliation': affiliation
      };

      items.push(Object.assign({}, productData, DataLayer.quoteProductInformation(product)));
    });

    window.dataLayer.push({
      'event': 'purchase',
      'ecommerce': {
        'transaction_id': transactionId,
        'affiliation': affiliation,
        'value': DataLayer.formatPrice(cartValue),
        'tax': DataLayer.formatPrice(tax),
        'shipping': DataLayer.formatPrice(shipping),
        'currency': DataLayer.currency,
        'coupon': couponCode,
        'items': items
      }
    });
  }
}
