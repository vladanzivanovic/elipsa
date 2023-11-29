import headerMapper from "../Mapper/HeaderMapper";

class HeaderDom {
    #mapper
    constructor() {
        if (!HeaderDom.instance) {
            this.#mapper = headerMapper;

            HeaderDom.instance = this;
        }

        return HeaderDom.instance;
    }

    updateLanguageDropDown(links)
    {
        for (const locale in links) {
            $(`${this.#mapper.localeDropDown} #locale_${locale}`).attr('href', links[locale][ROUTE_NAME]);
        }
    }
}

const headerDom = new HeaderDom();

Object.freeze(headerDom);

export default headerDom;
