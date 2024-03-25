import HttpClient from 'src/service/http-client.service';
import BaseWishlistStoragePlugin from 'src/plugin/wishlist/base-wishlist-storage.plugin';
import Storage from 'src/helper/storage/storage.helper';
import DomAccessHelper from 'src/helper/dom-access.helper';
import Cache from 'src/helper/cache.helper';

/**
 * @package checkout
 */
export default class WishlistPersistStoragePlugin extends BaseWishlistStoragePlugin {
    static options = {
        countUrl: '',
        ttl: 120000,
    };

    static key = 'sw-wishlist-cache';

    init() {
        super.init();
        this.httpClient = new HttpClient();
    }

    load() {
        this._merge(() => {
            Cache.get(WishlistPersistStoragePlugin.key, this.options.ttl, () => {
                return this.fetchValue();
            }).then((value) => {
                this.products = value;
                super.load();
            })
        });
    }

    fetchValue() {
        return new Promise((resolve) => {
            this.httpClient.get(this.options.countUrl, response => {
                resolve(JSON.parse(response));
            });
        });
    }

    refreshCache() {
        Cache.set(WishlistPersistStoragePlugin.key, this.options.ttl, this.products);
    }

    add(productId, router) {
        this.httpClient.post(router.path, null, response => {
            const res = JSON.parse(response);

            if (res.success) {
                super.add(productId);

                this.refreshCache();

                return;
            }

            console.warn('unable to add product to wishlist');
        });
    }

    remove(productId, router) {
        this.httpClient.post(router.path, null, response => {
            const res = JSON.parse(response);
            // even if the call returns false, the item should be removed from storage because it may be already deleted
            if (Object.prototype.hasOwnProperty.call(res, 'success')) {
                if (res.success === false) {
                    console.warn('unable to remove product to wishlist');
                }

                super.remove(productId);
                this.refreshCache();

                return;
            }

        });
    }

    /**
     * @private
     */
    _merge(callback) {
        this.storage = Storage;
        const key = 'wishlist-' + (window.salesChannelId || '');

        const productStr = this.storage.getItem(key);

        const products = JSON.parse(productStr);

        if (products) {
            this.httpClient.post(this.options.mergePath, JSON.stringify({
                'productIds' : Object.keys(products),
            }), response => {
                if (!response) {
                    throw new Error('Unable to merge product wishlist from anonymous user');
                }

                this.$emitter.publish('Wishlist/onProductMerged', {
                    products: products,
                });

                this.storage.removeItem(key);
                this._block = DomAccessHelper.querySelector(document, '.flashbags');
                this._block.innerHTML = response;
                this._pagelet();
                callback();
            });
        }
        callback();
    }

    /**
     * @private
     */
    _pagelet() {
        this.httpClient.post(this.options.pageletPath, '', response => {
            if (!response) {
                return;
            }

            this._block = DomAccessHelper.querySelector(document, '.cms-listing-row');
            this._block.innerHTML = response;
        });
    }
}
