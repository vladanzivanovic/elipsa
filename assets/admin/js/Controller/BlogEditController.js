import BlogEditMapper from "../Mapper/BlogEditMapper";
import SummerNote from "../Services/SummerNote";
import BlogEditService from "../Services/BlogEditService";
import BlogEditHandler from "../Handler/BlogEditHandler";
import DropZone from "../../../js/Services/DropZoneService";
import blogEditValidator from "../Validators/BlogEditValidator";
require ('select2/dist/js/select2.full.min');

class BlogEditController {
    constructor() {
        this.mapper = new BlogEditMapper();
        this.editService = new BlogEditService();
        this.validator = blogEditValidator;
        this.dropZone = DropZone(this.mapper.form);
        this.dropZone.init(this.mapper.form);

        this.summernote = new SummerNote();

        this.summernote.initialize(this.mapper.desc_rs, this.createCallBacksSummernote(this.mapper.desc_rs));
        this.summernote.initialize(this.mapper.desc_en, this.createCallBacksSummernote(this.mapper.desc_en));
        $('.dropdown-toggle').dropdown();

        this.mapper.blog_tags.select2();

        if (IS_EDIT) {
            this.dropZone.setFiles(IMAGES, 'blog');
        }

        this.validator.validate(this.mapper.form);

        this.registerEvents();
    }

    createCallBacksSummernote(el)
    {
        return {
            onImageUpload: files => {
                this.editService.sendSummernoteFile(el, files[0])
                    .then(response => {
                        el.summernote('insertImage', response.file_url, function ($image) {
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
        this.mapper.submitBtn.on('click', (e) => {
            const handler = new BlogEditHandler();

            handler.save(this.mapper);
        });
    }
}

export default BlogEditController;