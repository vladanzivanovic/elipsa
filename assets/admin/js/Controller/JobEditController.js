import BlogEditMapper from "../Mapper/BlogEditMapper";
import SummerNote from "../Services/SummerNote";
import BlogEditService from "../Services/BlogEditService";
import BlogEditHandler from "../Handler/BlogEditHandler";
import DropZone from "../../../js/Services/DropZoneService";
import blogEditValidator from "../Validators/BlogEditValidator";
import JobsHandler from "../Handler/JobsHandler";
import jobEditMapper from "../Mapper/JobEditMapper";
import jobEditValidator from "../Validators/JobEditValidator";
require ('select2/dist/js/select2.full.min');

class JobEditController {
    constructor() {
        this.mapper = jobEditMapper;
        this.validator = jobEditValidator;
        this.dropZone = DropZone($(this.mapper.form));
        this.dropZone.init($('[data-files="job"]'));

        this.summernote = new SummerNote();

        this.summernote.initialize($(this.mapper.desc_rs), this.summernote.createCallBacksSummernote($(this.mapper.desc_rs), 'job'));
        this.summernote.initialize($(this.mapper.desc_en), this.summernote.createCallBacksSummernote($(this.mapper.desc_en), 'job'));
        $('.dropdown-toggle').dropdown();

        if (IS_EDIT) {
            this.dropZone.setFiles(IMAGES, 'job');
        }

        this.validator.validate(this.mapper.form);

        this.registerEvents();
    }

    registerEvents()
    {
        $(this.mapper.submitBtn).on('click', (e) => {
            const handler = new JobsHandler();

            handler.save();
        });
    }
}

export default JobEditController;