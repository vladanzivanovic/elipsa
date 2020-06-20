import BlogEditMapper from "../Mapper/BlogEditMapper";
import SummerNote from "../Services/SummerNote";
import BlogEditService from "../Services/BlogEditService";
import BlogEditHandler from "../Handler/BlogEditHandler";
import BlogEditValidator from "../Validators/BlogEditValidator";
import DropZone from "../../../js/Services/DropZoneService";
import aboutUsPageMapper from "../Mapper/AboutUsPageMapper";
import AboutUsPageService from "../Services/AboutUsPageService";
import AboutUsHandler from "../Handler/AboutUsHandler";

class AboutUsPageController {
    constructor() {
        this.mapper = aboutUsPageMapper;
        this.editService = new AboutUsPageService();
        this.handler = new AboutUsHandler();

        this.summernote = new SummerNote();

        this.summernote.initialize($(this.mapper.desc_rs), this.createCallBacksSummernote(this.mapper.desc_rs));
        this.summernote.initialize($(this.mapper.desc_en), this.createCallBacksSummernote(this.mapper.desc_en));
        $('.dropdown-toggle').dropdown();

        this.registerEvents();
    }

    createCallBacksSummernote(el)
    {
        return {
            onImageUpload: files => {
                this.editService.sendSummernoteFile($(el), files[0])
                    .then(response => {
                        $(el).summernote('insertImage', response.file_url, function ($image) {
                            $image.attr('data-filename', response.file_name);
                        });
                    })
            },
            onMediaDelete: target => {
                this.editService.removeSummernoteImage(target[0].dataset.filename);
            }
        }
    }

    registerEvents()
    {
        $(this.mapper.submitBtn).on('click touchend', (e) => {
            e.preventDefault();
            e.stopPropagation();

            this.handler.save();
        });
    }
}

export default AboutUsPageController;