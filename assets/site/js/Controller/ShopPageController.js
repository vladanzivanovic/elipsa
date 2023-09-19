import shopPageMapper from "../Mapper/ShopPageMapper";
import shopPageDom from "../Dom/ShopPageDom";
import shopFilterManipulator from "../Manipulator/ShopFilterManipulator";
import shopFilterEvents from "../Events/ShopFilterEvents";
import shopPageOptionsManipulator from "../Manipulator/ShopPageOptionsManipulator";
import shopPageEvents from "../Events/ShopPageEvents";
import headerDom from "../Dom/HeaderDom";

class ShopPageController {
    #filterManipulator;
    #filterEvents;
    #pageOptionManipulator;
    #pageEvents;
    #headerDom;

    constructor() {
        this.mapper = shopPageMapper;
        this.dom = shopPageDom;
        this.#filterManipulator = shopFilterManipulator;
        this.#pageOptionManipulator = shopPageOptionsManipulator;
        this.#filterEvents = shopFilterEvents;
        this.#pageEvents = shopPageEvents;
        this.#headerDom = headerDom;

        this.#filterManipulator.setFilters(SEARCH_CRITERIA);
        this.#pageOptionManipulator.setPageOptions(PAGE_OPTIONS);
        this.#headerDom.updateLanguageDropDown(LINKS);

        // todo need to find a way to set all previously loaded items
        // if (localStorage.getItem('shopPageScroll') != null) {
        //     $(window).scrollTop(localStorage.getItem('shopPageScroll'));
        // }

        this.#filterEvents.registerEvents();
        this.#pageEvents.registerEvents();
    }
}

export default ShopPageController;
