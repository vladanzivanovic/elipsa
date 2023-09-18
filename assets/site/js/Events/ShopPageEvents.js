import shopPageMapper from "../Mapper/ShopPageMapper";
import shopPageOptionsManipulator from "../Manipulator/ShopPageOptionsManipulator";

class ShopPageEvents {
    #pageMapper;
    #pageOptionManipulator;

    constructor() {
        if (!ShopPageEvents.instance) {
            this.#pageMapper = shopPageMapper;
            this.#pageOptionManipulator = shopPageOptionsManipulator;

            ShopPageEvents.instance = this;
        }

        return ShopPageEvents.instance;
    }

    registerEvents()
    {
        $(this.#pageMapper.sortOption).on('change', async e => {
            this.#pageOptionManipulator.toggle(e, 'sort', e.currentTarget.value);
        });

        $(this.#pageMapper.limit).on('change', e => {
            this.#pageOptionManipulator.toggle(e, 'limit', e.currentTarget.value);
        });

        $(this.#pageMapper.filterBtnOpen).on('click', e => {
            $('body').addClass('disable-scroll');
            document.getElementById("myNav").style.height = "100%";
        });

        $(this.#pageMapper.filterBtnClose).on('click', e => {
            $('body').removeClass('disable-scroll');
            document.getElementById("myNav").style.height = "0%";
        })

        $(this.#pageMapper.loadMore).on('click', e => {
            this.#pageOptionManipulator.loadItems();
        });

        $(window).scroll(() => {
            this.#pageOptionManipulator.loadItemsOnScroll();
            localStorage.setItem("shopPageScroll", $(window).scrollTop());
        });

        $(window).on('disableScroll::off', e => {
            this.#pageOptionManipulator.setDisableScroll(false);
        });
    }
}

const shopPageEvents = new ShopPageEvents();

Object.freeze(shopPageEvents);

export default shopPageEvents;
