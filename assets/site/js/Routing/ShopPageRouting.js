import shopPageDom from "../Dom/ShopPageDom";

class ShopPageRouting {
    constructor() {
        this.dom = shopPageDom;
        this.params = {};

        if (Object.keys(SEARCH_CRITERIA).length > 0) {
            this.params = SEARCH_CRITERIA;
        }

        this.url =  Routing.generate(`${ROUTE_NAME}.${LOCALE}`);
        this.apiUrl =  Routing.generate(`${ROUTE_NAME.replace('site', 'site_api')}.${LOCALE}`);

        this.url += '/1';
        this.apiUrl += '/1';
    }

    toggleParam(paramName, paramValue, text, onlyOne)
    {
        if (!this.params.hasOwnProperty(paramName) || true === onlyOne) {
            this.params[paramName] = [];
        }

        const valueIndex = this.params[paramName].indexOf(paramValue);

        if (valueIndex > -1) {
            this.params[paramName].splice(valueIndex, 1);

            if (this.params[paramName].length === 0) {
                delete this.params[paramName];
            }

            return;
        }

        if (text) {
            this.dom.addCriteriaOnPage(paramName, paramValue, text);
        }

        this.params[paramName].push(paramValue);
    }

    generateUrl(){
        let params = '';

        for (let paramName in this.params) {
            params +=`/${Translator.trans(paramName, null, 'messages', LOCALE)}/${this.params[paramName].join('+')}`;
        }

        window.history.pushState({ path: this.url + params }, '', this.url + params);

        return this.apiUrl + params;
    }
}

export default ShopPageRouting;