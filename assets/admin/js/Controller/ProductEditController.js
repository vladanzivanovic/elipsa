import DropZoneService from "../../../js/Services/DropZoneService";
import ProductEditHandler from "../Handler/Product/ProductEditHandler";
import ProductDropZoneService from "../../../js/Services/ProductDropZoneService";
import productEditValidator from "../Validators/ProductEditValidator";
import Tipped from "@staaky/tipped";
import productEditMapper from "../Mapper/ProductEditMapper";
import productEditEvents from "../Event/ProductEditEvents";
import productEditManipulator from "../Manipulator/ProductEditManipulator";
import youtubeService from "../Services/YouTubeService";
require ('select2/dist/js/select2.full.min');

class ProductEditController {
    #mapper;
    #dropZone;
    #validator;
    #youtube;
    #handler;
    #productEditEvents;
    #productEditManipulator;

    constructor() {
        this.#mapper = productEditMapper;
        this.#dropZone = new ProductDropZoneService(DropZoneService());
        this.#validator = productEditValidator;
        this.#youtube = youtubeService;
        this.#handler = new ProductEditHandler(this.#youtube);
        this.#productEditEvents = productEditEvents;
        this.#productEditManipulator = productEditManipulator;

        this.initializeForm();

        this.#productEditEvents.registerEvents();
    }

    initializeForm()
    {
        this.#dropZone.init($('[data-files="mainImages"]'), {'colors': COLORS});
        this.initializeSelect();

        Tipped.create('.cleaning-icons');

        this.#validator.validate(this.#mapper.form);
    }

    initializeSelect() {
        this.#productEditManipulator.setSizes();

        if (IS_EDIT) {
            this.#dropZone.setFiles(IMAGES, 'mainImages');
            this.#youtube.setFromArray(YOUTUBES);
        }

        $(`${this.#mapper.form} select:not(.image-color)`).select2({
            width: '100%'
        });
    }
}

export default ProductEditController;
