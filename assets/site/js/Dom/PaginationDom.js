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

        let lastNextPage = data.currentPage + 3;
        let nextPage = data.nextPage;

        while (nextPage <= lastNextPage) {
            let url = location.href.replace(/\/1/g, '/'+nextPage);
            $(`<li class="pages"><a href="${url}">${nextPage}</a></li>`).insertBefore('.next');

            nextPage++;
        }

        let url = location.href.replace(/\/1/g, '/'+data.currentPage);
        $(`<li class="pages active"><a href="${url}">${data.currentPage}</a></li>`).insertAfter('.prev');


        return html;
    };
}

const paginationDom = new PaginationDom();
Object.freeze(paginationDom);

export default paginationDom;
