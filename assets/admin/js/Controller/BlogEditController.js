import SummerNote from "../Services/SummerNote";
import BlogEditService from "../Services/BlogEditService";
import BlogEditHandler from "../Handler/BlogEditHandler";
import DropZone from "../../../js/Services/DropZoneService";
import blogEditValidator from "../Validators/BlogEditValidator";
import blogEditMapper from "../Mapper/BlogEditMapper";
import baseEvents from "./BaseEvents";
import countrySelectionEvents from "../Event/CountrySelectionEvents";
require ('select2/dist/js/select2.full.min');

class BlogEditController {
    #baseEvents;
    #mapper;
    #editService;
    #validator;
    #dropZone;
    #countrySelectionEvents;
    
    constructor() {
        this.#baseEvents = baseEvents;
        this.#mapper = blogEditMapper;
        this.#editService = new BlogEditService();
        this.#validator = blogEditValidator;
        this.#dropZone = DropZone();
        this.#countrySelectionEvents = countrySelectionEvents;

        this.#dropZone.init($('[data-files="blog"]'));

        this.summernote = new SummerNote();

        for(const [locale, data] of Object.entries(LANGUAGES)) {
            const element = $(this.#mapper.fields[`description_${locale}`]);

            this.summernote.initialize(
                element,
                this.createCallBacksSummernote(element)
            );
        }
        $('.dropdown-toggle').dropdown();

        $(`${this.#mapper.form} select`).select2();

        if (IS_EDIT) {
            this.#dropZone.setFiles(IMAGES, 'blog');
        }

        this.#validator.validate();

        this.registerEvents();
    }

    createCallBacksSummernote(el)
    {
        return {
            onImageUpload: files => {
                this.#editService.sendSummernoteFile(el, files[0])
                    .then(response => {
                        el.summernote('insertImage', response.file_url, function ($image) {
                            $image.attr('data-filename', response.file_name);
                        });
                    })
            },
            onMediaDelete: target => {
                this.#editService.removeSummernoteImage(target[0].dataset.filename);
            }
        }
    }

    registerEvents()
    {
        $(this.#mapper.submitBtn).on('click', (e) => {
            const handler = new BlogEditHandler();

            handler.save(this.#mapper);
        });

        this.#countrySelectionEvents.registerEvents();
        this.#baseEvents.events();
    }
}

export default BlogEditController;
