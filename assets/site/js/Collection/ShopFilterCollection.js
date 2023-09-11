class ShopFilterCollection {
    #filterCollection;

    constructor() {
        if (!ShopFilterCollection.instance) {
            this.#filterCollection = {};

            ShopFilterCollection.instance = this;
        }

        return ShopFilterCollection.instance;
    }

    /**
     *
     * @param {Object} filterCollection
     */
    setFilters(filterCollection)
    {
        for(let filterName in filterCollection) {
            if (null === filterCollection[filterName]) {
                filterCollection[filterName] = [];
            }
        }

        this.#filterCollection = filterCollection;
    }

    setFilter(key, value)
    {
        this.#filterCollection[key] = value;

        return this.#filterCollection;
    }

    getFilter(key)
    {
        return this.#filterCollection[key];
    }

    getFilters()
    {
        return this.#filterCollection;
    }
}

const shopFilterCollection = new ShopFilterCollection();

Object.freeze(shopFilterCollection);

export default shopFilterCollection;
