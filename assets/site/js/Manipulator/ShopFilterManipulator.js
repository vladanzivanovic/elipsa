import 'webpack-jquery-ui';
import shopPageProvider from "../Provider/ShopPageProvider";
import shopPageDom from "../Dom/ShopPageDom";
import paginationDom from "../Dom/PaginationDom";
import loader from "../Dom/LoaderDom";
import shopFilterDom from "../Dom/ShopFilterDom";
import shopFilterMapper from "../Mapper/ShopFilterMapper";
import shopFilterCollection from "../Collection/ShopFilterCollection";
import shopOptionCollection from "../Collection/ShopOptionCollection";
import headerDom from "../Dom/HeaderDom";

class ShopFilterManipulator {
    #pageProvider;
    #filterCollection;
    #shopPageDom;
    #pagination;
    #loader;
    #shopFilterDom;
    #filterMapper;
    #optionCollection;
    #headerDom;

    constructor() {
        if (!ShopFilterManipulator.instance) {
            this.#pageProvider = shopPageProvider;
            this.#shopPageDom = shopPageDom;
            this.#pagination = paginationDom;
            this.#loader = loader;
            this.#shopFilterDom = shopFilterDom;
            this.#filterMapper = shopFilterMapper;
            this.#optionCollection = shopOptionCollection;
            this.#filterCollection = shopFilterCollection;
            this.#headerDom = headerDom;

            ShopFilterManipulator.instance = this;
        }

        return ShopFilterManipulator.instance;
    }

    /**
     *
     * @param {Object} filterCollection
     */
    setFilters(filterCollection)
    {
        this.#filterCollection.setFilters(filterCollection);

        for(let filterName in this.#filterCollection.getFilters()) {
            const elm = $(this.#filterMapper[filterName]);

            this.#shopFilterDom.setFilterCounterByElement(elm, filterCollection[filterName].length);

            this.#shopFilterDom.setFilterTags(filterCollection);
        }

        this.setFilterPrice(this.#filterCollection.getFilter('price'));
    }

    /**
     *
     * @param {array} price
     */
    setFilterPrice(price)
    {
        let length = 1;

        if (0 === price.length) {
            price = [0, PRICES[1]];
            length = 0;
        }

        $(this.#filterMapper.priceRange).slider({
            range: true,
            min: 0,
            max: PRICES[1],
            values: price,
            slide: (event, ui) => {
                $(this.#filterMapper.priceInput).val(`${ui.values[0]} RSD - ${ui.values[1]} RSD`);
            }
        });

        $(this.#filterMapper.priceInput).val(`${$(this.#filterMapper.priceRange).slider("values", 0)} RSD - ${$(this.#filterMapper.priceRange).slider("values", 1)} RSD`);

        this.#shopFilterDom.setFilterCounterByElement($(this.#filterMapper.priceRange), length);
    }

    async toggleFilter(e, name, value) {
        const elm = $(e.currentTarget);
        const filterCollection = this.#filterCollection.getFilters();
        let valueIndex = filterCollection[name].indexOf(value);

        loader.show();

        if (valueIndex > -1) {
            elm.removeClass('active');

            filterCollection[name].splice(valueIndex, 1);
        } else {
            elm.addClass('active');

            filterCollection[name].push(value);
        }

        if (name === 'price') {
            let price = value.split('-');

            if (price[0] == 0 && price[1] == PRICES[1]) {
                price = [];
            }

            filterCollection.price = price;
        }

        this.#optionCollection.setOption('page', 1);

        await this.#updateFilters(filterCollection);

        loader.hide();
    }

    async removeFilterTag(filter)
    {
        const filterValues = this.#filterCollection.getFilter(filter);

        for(const filterValue of filterValues) {
            $(this.#filterMapper[filter]).removeClass('active');
        }

        this.#filterCollection.setFilter(filter, []);

        this.#optionCollection.setOption('page', 1);

        await this.#updateFilters(this.#filterCollection.getFilters());
    }

    async #updateFilters(filterCollection)
    {
        try {
            const currentPage = this.#optionCollection.getOption('page');

            const filteredData = await this.#pageProvider.getProducts(
                {...filterCollection, ...this.#optionCollection.getOptions()}
            );

            $(window).trigger('disableScroll::off');

            this.#shopPageDom.generateProducts(
                filteredData.products,
                1 === currentPage,
                currentPage >= filteredData.pagination.totalPages
            );
            // this.#pagination.generate(filteredData.pagination);

            this.setFilters(filteredData.search);

            for (let filter in this.#filterCollection.getFilters()) {
                const elm = $(this.#filterMapper[filter]);
                const filterLength = this.#filterCollection.getFilter(filter).length;

                this.#shopFilterDom.setFilterCounterByElement(elm, filterLength);
            }

            this.#shopFilterDom.setFilterTags(this.#filterCollection.getFilters());

            this.setFilterPrice(this.#filterCollection.getFilter('price'));

            this.#setBrowserUrl(filteredData._web_links[LOCALE][ROUTE_NAME]);

            this.#headerDom.updateLanguageDropDown(filteredData._web_links);
        } catch (e) {
            console.log(e);
        }
    }

    #setBrowserUrl(url)
    {
        window.history.pushState({}, '', url);
    }
}

const shopFilterManipulator = new ShopFilterManipulator();

Object.freeze(shopFilterManipulator);

export default shopFilterManipulator;
