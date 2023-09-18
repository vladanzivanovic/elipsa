import shopPageMapper from "../Mapper/ShopPageMapper";

class PaginationDom {
    constructor() {
        if (!PaginationDom.instance) {
            this.mapper = shopPageMapper;

            PaginationDom.instance = this;
        }

        return PaginationDom.instance;
    }

    generate(data) {
        let html = ``;

        $('.pagination .pages').remove();
        $('.pagination .first').show();
        $('.pagination .prev').show();
        $('.pagination .next').show();
        $('.pagination .last').show();

        if (0 === data.totalPages) {
            $('.pagination .first').hide();
            $('.pagination .prev').hide();
            $('.pagination .next').hide();
            $('.pagination .last').hide();

            return;
        }

        $('.pagination .first').addClass(data.disableFirst ? 'disabled' : '');
        $('.pagination .prev').addClass(data.disableFirst ? 'disabled' : '');
        $('.pagination .next').addClass(data.disableLast ? 'disabled' : '');
        $('.pagination .last').addClass(data.disableLast ? 'disabled' : '');

        let lastNextPage = (data.currentPage + 3) > data.totalPages ? data.currentPage + 3 : data.totalPages;
        // let nextPage = data.nextPage === data.totalPages ? ;

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
