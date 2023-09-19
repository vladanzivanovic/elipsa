import shopFilterMapper from "../Mapper/ShopFilterMapper";

class ShopFilterDom {
    #filterMapper;
    #exceptionTagList = {
        'price': 'array_to_string',
        'search': 'string'
    };
    #excludeList = ['sort', 'limit'];

    constructor() {
        if (!ShopFilterDom.instance) {
            this.#filterMapper = shopFilterMapper;

            ShopFilterDom.instance = this;
        }

        return ShopFilterDom.instance;
    }

    setFilterCounterByElement(elm, total)
    {
        let totalText = `(${total})`;

        if (0 === total) {
            totalText = '';
        }

        elm.parents('.sidebar-box').find('.sidebar-accordion-button').find(this.#filterMapper.filterCounter).text(`${totalText}`);
    }

    setFilterTags(selectedFilters)
    {
        for(let filter in selectedFilters) {
            let filterValue = selectedFilters[filter];

            if (-1 < this.#excludeList.indexOf(filter)) {
                continue;
            }

            if (null === filterValue || 0 === filterValue.length) {
                this.removeFilterTag(filter);

                continue;
            }

            if (this.#exceptionTagList.hasOwnProperty(filter)) {

                switch (this.#exceptionTagList[filter]) {
                    case 'array_to_string':
                        filterValue = [`${selectedFilters[filter][0]} - ${selectedFilters[filter][1]}`];
                        break;
                    default:
                        filterValue = [selectedFilters[filter]];
                }

            }

            this.#setSelectedFilter(filter, filterValue);
        }
    }

    removeFilterTag(filter)
    {
        const filterElm = $(`a[data-selected-filter="${filter}"]`);

        if (0 === filterElm.length) {
            return;
        }

        filterElm.remove();
    }

    #setSelectedFilter(filter, values)
    {
        const filterElm = $(`a[data-selected-filter="${filter}"]`);
        const filterTransKey = `filter_text.${filter}`;

        let filterHtml = '';

        if (0 === filterElm.length) {
            filterHtml = `
                <a class="btn selected-filter-btn letter-capitalize" data-selected-filter="${filter}">
                    <span class="value-text">${Translator.trans(filterTransKey, null, 'messages', LOCALE)}: ${values.length}</span>
                    <span class="close"></span>
                </a>`;

            $(this.#filterMapper.searchCriteria).append(filterHtml);

            return;
        }

        $(`.value-text`, filterElm).text(`${Translator.trans(filterTransKey, null, 'messages', LOCALE)} (${values.length})`);
    }
}

const shopFilterDom = new ShopFilterDom();

Object.freeze(shopFilterDom);

export default shopFilterDom;
