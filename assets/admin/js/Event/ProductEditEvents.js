import productEditMapper from "../Mapper/ProductEditMapper";
import YoutubeService from "../Services/YouTubeService";
import ProductEditHandler from "../Handler/Product/ProductEditHandler";
import productEditManipulator from "../Manipulator/ProductEditManipulator";

class ProductEditEvents {
    #productEditMapper;
    #youtube;
    #handler;
    #productEditManipulator;

    constructor() {
        if(!ProductEditEvents.instance) {
            this.#productEditMapper = productEditMapper;
            this.#youtube = new YoutubeService();
            this.#handler = new ProductEditHandler(this.#youtube);
            this.#productEditManipulator = productEditManipulator;

            ProductEditEvents.instance = this;
        }

        return ProductEditEvents.instance;
    }

    registerEvents() {
        $(this.#productEditMapper.submitBtn).on('click', e => {
            this.#handler.save();
        });

        $(this.#productEditMapper.sizeAddBtn).on('click', e => {
            this.#productEditManipulator.addSizeRow(null, null);
        });

        $(document).on('click', this.#productEditMapper.sizeRemoveBtn, e => {
            const row = $(e.currentTarget).closest('tr');

            this.#productEditManipulator.removeSizeRow(row);
        });
    }
}

const productEditEvents = new ProductEditEvents();

Object.freeze(productEditEvents);

export default productEditEvents;
