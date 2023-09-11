import shopPageDom from "../Dom/ShopPageDom";
import paginationDom from "../Dom/PaginationDom";
import loader from "../Dom/LoaderDom";
import shopFilterManipulator from "./ShopFilterManipulator";
import shopPageProvider from "../Provider/ShopPageProvider";
import shopOptionCollection from "../Collection/ShopOptionCollection";
import shopFilterCollection from "../Collection/ShopFilterCollection";
import headerDom from "../Dom/HeaderDom";

class ShopPageOptionsManipulator {
    #optionCollection;
    #filterCollection;
    #shopPageDom;
    #pagination;
    #filterManipulator;
    #pageProvider;
    #headerDom;

    constructor() {
        if (!ShopPageOptionsManipulator.instance) {
            this.#shopPageDom = shopPageDom;
            this.#pagination = paginationDom;
            this.#filterManipulator = shopFilterManipulator;
            this.#pageProvider = shopPageProvider;
            this.#optionCollection = shopOptionCollection;
            this.#filterCollection = shopFilterCollection;
            this.#headerDom = headerDom;

            ShopPageOptionsManipulator.instance = this;
        }

        return ShopPageOptionsManipulator.instance;
    }

    /**
     *
     * @param {Object} optionCollection
     */
    setPageOptions(optionCollection)
    {
        this.#optionCollection.setOptions(optionCollection);
    }

    async toggle(e, name, value) {
        loader.show();

        this.#optionCollection.setOption(name, value);

        if (PAGE_OPTIONS_DEFAULT_VALUES[name] === value) {
            this.#optionCollection.setOption(name, null);
        }

        await this.#update();

        loader.hide();
    }

    async #update()
    {
        try {
            const data = await this.#pageProvider.getProducts(
                {...this.#optionCollection.getOptions(), ...this.#filterCollection.getFilters()}
            );

            this.#shopPageDom.generateProducts(data.products);
            this.#pagination.generate(data.pagination);

            this.#setBrowserUrl(data._links[LOCALE]);

            this.#headerDom.updateLanguageDropDown(data._links);
        } catch (e) {
            console.log(e);
        }
    }

    #setBrowserUrl(url)
    {
        window.history.pushState({}, '', url);
    }
}

const shopPageOptionsManipulator = new ShopPageOptionsManipulator();

Object.freeze(shopPageOptionsManipulator);

export default shopPageOptionsManipulator;
