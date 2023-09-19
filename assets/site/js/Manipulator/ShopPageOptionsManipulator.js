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
    #loadPageFinished = true;
    #disableScroll = false;
    #timeoutId = undefined;

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

        this.#optionCollection.setOption('page', 1);

        await this.#update();

        loader.hide();
    }

    loadItems() {
        const currentPage = this.#optionCollection.getOption('page');

        this.#optionCollection.setOption('page', currentPage+1);

        return this.#update();
    }

    loadItemsOnScroll()
    {
        if (true === this.#disableScroll) {
            return;
        }

        const documentHeight = $(document).height();
        const footerHeight = Math.floor($('.footer-top-area').height() + $('.footer-bottom-area').height());
        const scrollTopPosition = $(window).scrollTop();
        const scrollPos = Math.floor($(window).height() + scrollTopPosition);
        const shouldTriggerAjax = documentHeight * 0.4 < scrollPos;

        if (typeof this.#timeoutId !== undefined) {
            clearTimeout(this.#timeoutId);
        }

        if (documentHeight - footerHeight <= scrollPos) {
            this.#disableScroll = true;

            return;
        }

        this.#timeoutId = setTimeout(() => {
            if (this.#loadPageFinished && shouldTriggerAjax) {
                this.#loadPageFinished = false;

                const loadItemsPromise = this.loadItems();

                loadItemsPromise.then(() => {
                    this.#loadPageFinished = true;
                });
            }
        }, 100);
    }

    setDisableScroll(disableScroll)
    {
        this.#disableScroll = disableScroll;
    }

    async #update()
    {
        try {
            const data = await this.#pageProvider.getProducts(
                {...this.#optionCollection.getOptions(), ...this.#filterCollection.getFilters()}
            );
            const currentPage = this.#optionCollection.getOption('page');

            this.#disableScroll = false;

            this.#shopPageDom.generateProducts(
                data.products,
                1 === currentPage,
                currentPage >= data.pagination.totalPages
            );
            // this.#pagination.generate(data.pagination);

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
