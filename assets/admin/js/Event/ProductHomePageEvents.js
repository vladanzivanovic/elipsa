import productBulkApiHandler from "../Handler/Product/ProductBulkApiHandler";
import toastrService from "../../../js/Services/ToastrService";

class ProductHomePageEvents {
    #bulkApiHandler;
    #toastr;

    constructor() {
        this.#bulkApiHandler = productBulkApiHandler;
        this.#toastr = toastrService;
    }

    registerEvents()
    {
        $('#position-up-rs').sortable();
        $('#position-up-ba').sortable();
        $('#position-down-rs').sortable();
        $('#position-down-ba').sortable();

        $('#submit_btn').on('click', async e => {
            const products = {};

            this.#toastr.showLoadingMessage();

            $('.position-wrapper').each((index, element) => {
                const children = $(element).children();
                const homePosition = $(element).data('homePosition');
                const country = $(element).data('country');

                if (products[homePosition] === undefined) {
                    products[homePosition] = {};
                }

                if (products[homePosition][country] === undefined) {
                    products[homePosition][country] = [];
                }

                $.each(children, (index,element) => {
                    products[homePosition][country].push($(element).data('productOptionId'));
                });
            })

            await this.#bulkApiHandler.changeHomePagePosition(products)
                .then(response => {
                    this.#toastr.success('Uspešno ste izmenili redosled proizvoda');
                })
                .fail(error => {
                    this.#toastr.error(Translator.trans('generic_error', null, 'message', LOCALE))
                })
        });
    }
}

const productHomePageEvents = new ProductHomePageEvents();

Object.freeze(productHomePageEvents);

export default productHomePageEvents;
