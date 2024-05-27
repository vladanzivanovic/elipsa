import productEditMapper from "../Mapper/ProductEditMapper";
import YoutubeService from "../Services/YouTubeService";
import ProductEditHandler from "../Handler/Product/ProductEditHandler";
import productEditManipulator from "../Manipulator/ProductEditManipulator";
import countrySelectionEvents from "./CountrySelectionEvents";

class ProductEditEvents {
    #productEditMapper;
    #youtube;
    #handler;
    #productEditManipulator;
    #countrySelectionEvents;

    constructor() {
        if(!ProductEditEvents.instance) {
            this.#productEditMapper = productEditMapper;
            this.#youtube = new YoutubeService();
            this.#handler = new ProductEditHandler(this.#youtube);
            this.#productEditManipulator = productEditManipulator;
            this.#countrySelectionEvents = countrySelectionEvents;

            ProductEditEvents.instance = this;
        }

        return ProductEditEvents.instance;
    }

    registerEvents() {
        $(this.#productEditMapper.submitBtn).on('click', e => {
            this.#handler.save();
        });

        for(const countryCode in COUNTRIES) {
            $(this.#productEditMapper.sizes[countryCode].addBtn).on('click', e => {
                this.#productEditManipulator.addSizeRow(countryCode, null, null);
            });

            $(document).on('click', this.#productEditMapper.sizes[countryCode].removeBtn, e => {
                const row = $(e.currentTarget).closest('tr');

                this.#productEditManipulator.removeSizeRow(row);
            });
        }

        this.#countrySelectionEvents.registerEvents();
    }
}

const productEditEvents = new ProductEditEvents();

Object.freeze(productEditEvents);

export default productEditEvents;
