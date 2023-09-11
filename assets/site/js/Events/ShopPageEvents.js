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

        $(this.#pageMapper.filterBtnOpen).on('click touchend', e => {
            $('body').addClass('disable-scroll');
            document.getElementById("myNav").style.height = "100%";
        });

        $(this.#pageMapper.filterBtnClose).on('click touchend', e => {
            $('body').removeClass('disable-scroll');
            document.getElementById("myNav").style.height = "0%";
        })
    }
}

const shopPageEvents = new ShopPageEvents();

Object.freeze(shopPageEvents);

export default shopPageEvents;
