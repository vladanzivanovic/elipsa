import ShopPageMapper from "../Mapper/ShopPageMapper";

class PaginationDom {
    constructor() {
        if (!PaginationDom.instance) {
            this.mapper = new ShopPageMapper();

            PaginationDom.instance = this;
        }

        return PaginationDom.instance;
    }

    generate(data) {
        let html = ``;

        $('.pagination .pages').remove();

        const prevElm = $('.pagination .prev');

        $('.pagination .first').addClass(data.disableFirst ? 'disabled' : '');
        prevElm.addClass(data.disableFirst ? 'disabled' : '');
        $('.pagination .next').addClass(data.disableLast ? 'disabled' : '');
        $('.pagination .last').addClass(data.disableLast ? 'disabled' : '');

        let i = data.totalPages;

        while( i > 0 ) {
            let url = location.href.replace(/\/1/g, '/'+i);
            $(`<li class="pages ${i === 1 ? 'active' : ''}"><a href="${url}">${i}</a></li>`).insertAfter('.prev');

            i--;
        }

        return html;
    };
}

const paginationDom = new PaginationDom();
Object.freeze(paginationDom);

export default paginationDom;