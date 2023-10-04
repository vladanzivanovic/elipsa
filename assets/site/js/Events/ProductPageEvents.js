import productPageMapper from "../Mapper/ProductPageMapper";
import productPageService from "../Service/ProductPageService";
import productPageHandler from "../Handler/ProductPageHandler";

class ProductPageEvents {
    #mapper;
    #productPageService;
    #productPageHandler;

    constructor() {
        if (!ProductPageEvents.instance) {
            this.#mapper = productPageMapper;
            this.#productPageService = productPageService;
            this.#productPageHandler = productPageHandler;

            ProductPageEvents.instance = this;
        }

        return ProductPageEvents.instance;
    }

    registerEvents() {
        $(this.#mapper.color).on('click', e => {
            e.preventDefault();
            e.stopPropagation();

            this.#productPageService.showImagesByColor($(e.currentTarget));
        });

        $(this.#mapper.size).on('click', e => {
            e.preventDefault();
            e.stopPropagation();

            this.#productPageService.toggleActiveSize(e.currentTarget);
        });

        $(this.#mapper.addBtn).on('click', e => {
            this.#productPageHandler.save();
        });

        $(this.#mapper.notifyMeBtn).on('click', async e => {
            await this.#productPageHandler.sizeNotification();
        });
    }
}

const productPageEvents =  new ProductPageEvents();

Object.freeze(productPageEvents);

export default productPageEvents;
