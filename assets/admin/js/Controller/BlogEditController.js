import BlogEditMapper from "../Mapper/BlogEditMapper";
import SummerNote from "../Services/SummerNote";
import BlogEditService from "../Services/BlogEditService";
import BlogEditHandler from "../Handler/BlogEditHandler";
import BlogEditValidator from "../Validators/BlogEditValidator";
import DropZoneMapper from "../Mapper/DropZoneMapper";
import DropZone from "../../../js/Services/DropZoneService";

require('select2/dist/js/select2.full.min');

class BlogEditController {
    constructor() {
        this.mapper = new BlogEditMapper();
        this.editService = new BlogEditService();
        this.dropZone = DropZone();
        this.dropZone.init(this.mapper.form);

        this.summernote = new SummerNote();

        this.summernote.initialize(this.mapper.desc_rs, this.createCallBacksSummernote(this.mapper.desc_rs));
        this.summernote.initialize(this.mapper.desc_en, this.createCallBacksSummernote(this.mapper.desc_en));

        this.setSelect2(this.mapper.blog_tags);

        if (window.isEdit) {
            this.dropZone.setFilesFromUrl(window.images, 'mainImages');
        }

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

    setSelect2(elm)
    {
        elm.select2({
            tags: true
        });
    }

    registerEvents()
    {
        this.mapper.submitBtn.on('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (this.validator) {
                this.validator.destroy();
            }
            this.validator = null;
            this.validator = BlogEditValidator().validate(this.mapper.form);

            let ajaxObject = {
                type: "POST",
                url: Routing.generate('admin.api_create_blog'),
                dataType: 'json'
            };

            if (window.isEdit) {
                ajaxObject.url = Routing.generate('admin.api_edit_blog', { slug: window.alias });
            }

            BlogEditHandler.save(this.mapper, ajaxObject);
        });

        DropZoneMapper().dropzone.on('click touchend', DropZoneMapper().fields.close, e => {
            this.dropZone.deleteFile(e.currentTarget);
        });
    }
}

export default BlogEditController;