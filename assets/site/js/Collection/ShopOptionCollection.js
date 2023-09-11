class ShopOptionCollection {
    #optionCollection;

    constructor() {
        if (!ShopOptionCollection.instance) {
            this.#optionCollection = {};

            ShopOptionCollection.instance = this;
        }

        return ShopOptionCollection.instance;
    }

    /**
     *
     * @param {Object} optionCollection
     */
    setOptions(optionCollection)
    {
        for(let filterName in optionCollection) {
            if (null === optionCollection[filterName]) {
                optionCollection[filterName] = [];
            }
        }

        this.#optionCollection = optionCollection;
    }

    setOption(key, value)
    {
        this.#optionCollection[key] = value;

        return this.#optionCollection;
    }

    getOption(key)
    {
        return this.#optionCollection[key];
    }

    getOptions()
    {
        return this.#optionCollection;
    }
}

const shopOptionCollection = new ShopOptionCollection();

Object.freeze(shopOptionCollection);

export default shopOptionCollection;
