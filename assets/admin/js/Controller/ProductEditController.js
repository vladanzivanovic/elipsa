import DropZoneService from "../../../js/Services/DropZoneService";
import ProductEditHandler from "../Handler/Product/ProductEditHandler";
import ProductDropZoneService from "../../../js/Services/ProductDropZoneService";
import productEditValidator from "../Validators/ProductEditValidator";
import Tipped from "@staaky/tipped";
import YoutubeService from "../Services/YouTubeService";
import productEditMapper from "../Mapper/ProductEditMapper";
require ('select2/dist/js/select2.full.min');


class ProductEditController {
    #mapper;
    #dropZone;
    #validator;
    #youtube;
    #handler;
    constructor() {
        this.#mapper = productEditMapper;
        this.#dropZone = new ProductDropZoneService(DropZoneService());
        this.#validator = productEditValidator;
        this.#youtube = new YoutubeService();
        this.#handler = new ProductEditHandler(this.#youtube);

        this.initializeForm();

        this.registerEvents();
    }

    initializeForm()
    {
        this.#dropZone.init($('[data-files="mainImages"]'), {'colors': COLORS});
        this.initializeSelect();

        Tipped.create('.cleaning-icons');

        this.#validator.validate(this.#mapper.form);
    }

    initializeSelect() {
        this.#mapper.tags.select2();
        this.#mapper.category.select2();
        this.#mapper.badge.select2();
        this.#mapper.sizes.select2();

        if (IS_EDIT) {
            this.#dropZone.setFiles(IMAGES, 'mainImages');
            this.#youtube.setFromArray(YOUTUBES);
        }
    }

    registerEvents() {
        $(this.#mapper.submitBtn).on('click touchend', e => {
            this.#handler.save();
        })
    }
}

export default ProductEditController;
