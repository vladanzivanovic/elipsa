import 'summernote/dist/summernote';

class SummerNote {
    constructor()
    {
        this.toolbar = [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['link', 'picture']],
        ];
    }

    initialize(el, callBacks)
    {
        const options = {toolbar: this.toolbar};

        if (callBacks) {
           options.callbacks = callBacks;
        }

        el.summernote(options);
    }

    sendSummernoteFile(el, file, entity) {
        let data = new FormData();
        data.set('tmp_image', file);
        data.set('entity', entity);

        return $.ajax({
            type: 'POST',
            url: Routing.generate('admin.summernote_image_resize'),
            data: data,
            contentType: false,
            processData: false,
        });
    }

    removeSummernoteImage(filename) {
        return $.ajax({
            type: 'DELETE',
            url: Routing.generate('admin.remove_summernote_image', {filename}),
            dataType: 'json'
        })
    }

    createCallBacksSummernote(el)
    {
        return {
            onImageUpload: files => {
                this.summernote.sendSummernoteFile(el, files[0])
                    .then(response => {
                        el.summernote('insertImage', response.file_url, function ($image) {
                            $image.attr('data-filename', response.file_name);
                        });
                    })
            },
            onMediaDelete: target => {
                this.summernote.removeSummernoteImage(target[0].dataset.filename);
            }
        }
    }
}

export default SummerNote;