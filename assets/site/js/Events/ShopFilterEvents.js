import shopFilterMapper from "../Mapper/ShopFilterMapper";
import shopFilterManipulator from "../Manipulator/ShopFilterManipulator";

class ShopFilterEvents {
    #filterMapper;
    #filterManipulator;

    constructor() {
        if (!ShopFilterEvents.instance) {
            this.#filterMapper = shopFilterMapper;
            this.#filterManipulator = shopFilterManipulator;

            $.scrollUp.init({
                scrollTrigger: $('<a/>', {
                    id: 'scroll-to-products',
                    href: '#products',
                    class: 'hide',
                }),
                scrollTarget: 50,
                easingType: 'linear',
                scrollSpeed: 1500,
                animation: 'fade'
            });

            ShopFilterEvents.instance = this;
        }

        return ShopFilterEvents.instance;
    }

    registerEvents()
    {
        $(this.#filterMapper.categories).on('click touchend', e => {
            this.#filterManipulator.toggleFilter(e, e.currentTarget.dataset.searchName, e.currentTarget.dataset.search);
        });
        $(this.#filterMapper.colors).on('click touchend', async e => {
            this.#filterManipulator.toggleFilter(e, e.currentTarget.dataset.searchName, e.currentTarget.dataset.search);
        });
        $(this.#filterMapper.collections).on('click touchend', async e => {
            this.#filterManipulator.toggleFilter(e, e.currentTarget.dataset.searchName, e.currentTarget.dataset.search);
        });
        $(this.#filterMapper.seasons).on('click touchend', async e => {
            this.#filterManipulator.toggleFilter(e, e.currentTarget.dataset.searchName, e.currentTarget.dataset.search);
        });
        $(this.#filterMapper.attributes).on('click touchend', async e => {
            this.#filterManipulator.toggleFilter(e, e.currentTarget.dataset.searchName, e.currentTarget.dataset.search);
        });
        $(this.#filterMapper.sizes).on('click touchend', async e => {
            this.#filterManipulator.toggleFilter(e, e.currentTarget.dataset.searchName, e.currentTarget.innerText);
        });
        $(this.#filterMapper.priceRange).on('slidestop', (event, ui) => {

            const elm = $(`.selected-filter-btn[data-name="price"]`);

            if (elm.length > 0) {
                elm.remove();
            }

            this.#filterManipulator.toggleFilter(event, 'price', ui.values.join('-'), ui.values.join('-'), true);
        });

        $(document).on('click', '.selected-filter-btn', async e => {
            const name = e.currentTarget.dataset.selectedFilter;

            this.#filterManipulator.removeFilterTag(name);
        });
    }
}

const shopFilterEvents = new ShopFilterEvents();

Object.freeze(shopFilterEvents);

export default shopFilterEvents;
