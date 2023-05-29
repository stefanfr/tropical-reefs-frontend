export class AppStorage {
  constructor(storage) {
    this.storage = storage || window.localStorage;

    if (!this.isSupported()) {
      throw new Error('Storage is not supported by browser!');
    }
  }

  hasItem(key) {
    return null !== this.storage.getItem(key);
  }

  setItem(key, value) {
    if ('undefined' !== typeof value && 'undefined' !== typeof key) {
      this.storage.setItem(key, JSON.stringify(value));
    }
  }

  getItem(key, _default) {
    return this.hasItem(key) ? JSON.parse(this.storage.getItem(key)) : _default;
  }

  getRaw(key) {
    return JSON.parse(this.storage.getItem(key));
  }

  removeItem(key) {
    this.storage.removeItem(key);
  }

  clear() {
    this.storage.clear();
  }

  isSupported() {
    let supported = true;

    if (!this.storage) {
      supported = false;
    }

    return supported;
  }
}

const appLocalStorage = new AppStorage();
const appSessionStorage = new AppStorage(window.sessionStorage);

export {appLocalStorage, appSessionStorage};
