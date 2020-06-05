import 'webpack-jquery-ui';
import ShopPageMapper from "../Mapper/ShopPageMapper";
import ShopPageRouting from "../Routing/ShopPageRouting";
import ShopPageService from "../Service/ShopPageService";
import shopPageDom from "../Dom/ShopPageDom";

class ShopPageController {
    constructor() {
        this.mapper = new ShopPageMapper();
        this.router = new ShopPageRouting();
        this.shopService = new ShopPageService();
        this.dom = shopPageDom;

        this.showSelectedFiltersOnLoad();

        this.registerEvents();
    }

    showSelectedFiltersOnLoad() {
        $.each($('.sidebar-widget .active'), (i, elm) => {
            this.dom.addCriteriaOnPage(elm.dataset.searchName, elm.dataset.search, elm.innerText);
        });

        if (SEARCH_CRITERIA.hasOwnProperty('price')) {
            this.dom.addCriteriaOnPage(Translator.trans('price', null, 'message', LOCALE), SEARCH_CRITERIA.price, SEARCH_CRITERIA.price, true);
        }
    }

    registerEvents() {
        this.mapper.category.on('click touchend', e => {
            this.toggleFilter(e, e.currentTarget.dataset.searchName, e.currentTarget.dataset.search, e.currentTarget.innerText, false);
        });
        this.mapper.color.on('click touchend', e => {
            this.toggleFilter(e, 'color', e.currentTarget.dataset.search, e.currentTarget.innerText, false);
        });
        this.mapper.size.on('click touchend', e => {
            this.toggleFilter(e, 'size', e.currentTarget.innerText, e.currentTarget.innerText, false);
        });
        this.mapper.sortOption.on('change', e => {
            this.toggleFilter(e, 'sort', e.currentTarget.value, null, true);
        });
        this.mapper.limit.on('change', e => {
            this.toggleFilter(e, 'limit', e.currentTarget.value, null, true);
        });

        $(document).on('click touchend', '.selected-filter-btn', e => {
            const name = e.currentTarget.dataset.name;
            const value = e.currentTarget.dataset.value;

            this.router.toggleParam(name, value);

            this.shopService.applyFilter(this.router.generateUrl());

            $(e.currentTarget).remove();
        });

        this.mapper.priceRange.on('slidestop', (event, ui) => {

            const elm = $(`.selected-filter-btn[data-name="price"]`);

            if (elm.length > 0) {
                elm.remove();
            }

            this.toggleFilter(event, 'price', ui.values.join('-'), ui.values.join('-'), true);
        });

        this.mapper.filterBtnOpen.on('click touchend', e => {
            $('body').addClass('disable-scroll');
            document.getElementById("myNav").style.height = "100%";
        });

        this.mapper.filterBtnClose.on('click touchend', e => {
            $('body').removeClass('disable-scroll');
            document.getElementById("myNav").style.height = "0%";
        })
    }

    toggleFilter(e, name, value, text, onlyOne) {
        e.preventDefault();
        e.stopPropagation();

        const elm = $(e.currentTarget);

        if (null !== text) {
            if (elm.hasClass('active')) {
                elm.removeClass('active');
            } else {
                elm.addClass('active');
            }
        }

        this.router.toggleParam(name, value, text, onlyOne);

        const apiUrl = this.router.generateUrl();

        this.shopService.applyFilter(apiUrl);

        this.mapper.filterBtnClose.click();
    }
}

export default ShopPageController;