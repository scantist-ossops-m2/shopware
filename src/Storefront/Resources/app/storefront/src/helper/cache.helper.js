import Storage from 'src/helper/storage/storage.helper';

export default class Cache {

    static get(key, ttl, closure) {
        let stored = Storage.getItem(key);

        if (!stored) {
            return this.refresh(key, ttl, closure);
        }

        stored = JSON.parse(stored);

        const time = stored.timestamp;
        const now = new Date().getTime();

        if (now - time > ttl) {
            return this.refresh(key, ttl, closure);
        }

        return new Promise((resolve) => {
            resolve(stored.value);
        });
    }

    static set(key, ttl, value) {
        const stored = JSON.stringify({
            timestamp: new Date().getTime(),
            value: value,
        });

        Storage.setItem(key, stored);
    }

    static refresh(key, ttl, closure) {
        const value = closure();

        return new Promise((resolve) => {
            value.then((val) => {
                this.set(key, ttl, val);
                resolve(val);
            });
        });
    }
}
