import BlogEditMapper from "../Mapper/BlogEditMapper";
import SummerNote from "../Services/SummerNote";
import BlogEditService from "../Services/BlogEditService";
import BlogEditHandler from "../Handler/BlogEditHandler";
import DropZone from "../../../js/Services/DropZoneService";
import descriptionEditMapper from "../Mapper/DescriptionEditMapper";
import DescriptionHandler from "../Handler/DescriptionHandler";
require ('select2/dist/js/select2.full.min');

class DescriptionEditController {
    constructor() {
        this.mapper = descriptionEditMapper;
        this.summernote = new SummerNote();
        this.handler = new DescriptionHandler();

        this.summernote.initialize($(this.mapper.desc_rs), this.summernote.createCallBacksSummernote($(this.mapper.desc_rs)));
        this.summernote.initialize($(this.mapper.desc_en), this.summernote.createCallBacksSummernote($(this.mapper.desc_en)));
        $('.dropdown-toggle').dropdown();

        this.registerEvents();
    }

    registerEvents()
    {
        $(this.mapper.submitBtn).on('click touchend', (e) => {
            e.preventDefault();
            e.stopPropagation();

            this.handler.save(this.mapper);
        });
    }
}

export default DescriptionEditController;